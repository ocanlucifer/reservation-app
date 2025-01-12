<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\VisitReservation;
use App\Models\BuildingSchedule;

class VisitScheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $perPage = $request->input('per_page', 10);
        $is_available = $request->input('is_available', '');
        $is_booked = $request->input('is_booked', '');
        $tg_req = $request->input('t_g_req', '');
        $tg_assign = $request->input('t_g_assign', '');
        $is_confirm = $request->input('is_confirm', '');

        if ($sortBy == 'tanggal') {
            $sortBy = 'schedule.tanggal';
        }

        // Periksa dan update status jadwal yang sudah lewat
        VisitReservation::WhereHas('schedule', function ($q) use ($search) {
                            $q->where('tanggal', '<', Carbon::now()->toDateString()); // Filter berdasarkan tanggal jadwal
                        })
            ->where('is_available', true) // Ganti 'tersedia' dengan status aktif Anda
            ->update(['is_available' => false]); // Ganti 'tidak tersedia' dengan status tidak aktif Anda


        // Ambil data jadwal gedung
        $visitSchedules = VisitReservation::with(['schedule', 'creator', 'updater', 'humas', 'visitor', 'koordinator', 'tourGuide'])
        ->join('building_schedules as schedule', 'visit_reservations.building_schedule_id', '=', 'schedule.id') // Gabung ke tabel schedule
        ->when($search, function ($query, $search) {
            $query->whereHas('schedule.building', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%"); // Filter berdasarkan nama gedung
            })
            ->orWhereHas('schedule', function ($q) use ($search) {
                $q->where('tanggal', 'like', "%{$search}%"); // Filter berdasarkan tanggal jadwal
            });
        })
        ->Where('visit_reservations.is_available','like', "%{$is_available}%")
        ->Where('visit_reservations.is_booked','like', "%{$is_booked}%")
        ->Where('visit_reservations.tour_guide_requested','like', "%{$tg_req}%")
        ->Where('visit_reservations.tour_guide_assign','like', "%{$tg_assign}%")
        ->Where('visit_reservations.is_confirm','like', "%{$is_confirm}%")
        ->orderBy($sortBy, $order)
        ->select('visit_reservations.*')
        ->paginate($perPage);


        if ($request->ajax()) {
            return view('visit_schedule.table', compact('visitSchedules', 'search', 'sortBy', 'order', 'perPage'));
        }

        return view('visit_schedule.index', compact('visitSchedules', 'search', 'sortBy', 'order', 'perPage'));
    }

    public function destroy($id)
    {
        // Cari data jadwal berdasarkan ID
        $visitSchedule = VisitReservation::find($id);

        if (!$visitSchedule) {
            return response()->json(['error' => 'Jadwal Kunjungan Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal tidak memiliki reservasi aktif
        if ($visitSchedule->is_booked) {
            return response()->json(['error' => 'Jadwal tidak dapat dihapus karena memiliki reservasi aktif!'], 422);
        }

        $buildingSchedule = BuildingSchedule::find($visitSchedule->building_schedule_id);
        $buildingSchedule->is_available = true;
        $buildingSchedule->humas_id     = null;
        $buildingSchedule->is_booked    = false;
        $buildingSchedule->booked_date  = null;
        $buildingSchedule->save();

        // Hapus data jadwal
        $visitSchedule->delete();

        return response()->json(['success' => 'Data Jadwal Kunjungan Berhasil Dihapus!']);
    }

    public function toggleStatus($id)
    {
        // Cari data jadwal berdasarkan ID
        $visitSchedule = VisitReservation::find($id);

        if (!$visitSchedule) {
            return response()->json(['error' => 'Jadwal Kunjungan Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal memiliki waktu yang valid untuk diaktifkan
        $currentDateTime = now();
        $scheduleEndDateTime = $visitSchedule->schedule->tanggal . ' ' . $visitSchedule->schedule->end_time;

        if ($visitSchedule->is_available && $currentDateTime > $scheduleEndDateTime) {
            return response()->json(['error' => 'Tidak dapat mengaktifkan jadwal yang telah berakhir!'], 422);
        }

        // Toggle status
        $visitSchedule->is_available = !$visitSchedule->is_available;
        $visitSchedule->save();

        return response()->json(['success' => 'Status Jadwal Kunjungan Berhasil Diperbarui!']);
    }

    public function update(Request $request)
    {
        // Cari data jadwal berdasarkan ID
        $visitSchedule = VisitReservation::find($request->id);

        if (!$visitSchedule) {
            return response()->json(['error' => 'Jadwal Gedung Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal memiliki waktu yang valid untuk diaktifkan
        $currentDateTime = now();
        $scheduleEndDateTime = $visitSchedule->schedule->tanggal . ' ' . $visitSchedule->schedule->end_time;

        if ($visitSchedule->is_booked && $currentDateTime > $scheduleEndDateTime) {
            return response()->json(['error' => 'Tidak dapat memesan jadwal yang telah berakhir!'], 422);
        }

        $visitSchedule->is_booked           = true;
        $visitSchedule->is_available        = false;
        $visitSchedule->visitor_id          = Auth::user()->id;
        $visitSchedule->booked_date         = now();
        $visitSchedule->visitor_company     = $request->visitor_company;
        $visitSchedule->visitor_address     = $request->visitor_address;
        $visitSchedule->visitor_purphose    = $request->visitor_purphose;
        $visitSchedule->visitor_contact     = $request->visitor_contact;
        $visitSchedule->visitor_person      = $request->visitor_person;
        $visitSchedule->visitor_note        = $request->visitor_note;
        $visitSchedule->save();

        return response()->json(['success' => 'Reservasi Berhasi!']);
    }

    public function cancel_booking($id)
    {
        // Cari data jadwal berdasarkan ID
        $visitSchedule = VisitReservation::find($id);

        if (!$visitSchedule) {
            return response()->json(['error' => 'Jadwal Gedung Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal memiliki waktu yang valid untuk diaktifkan
        $currentDateTime = now();
        $scheduleEndDateTime = $visitSchedule->schedule->tanggal . ' ' . $visitSchedule->schedule->end_time;

        if ($visitSchedule->is_booked && $currentDateTime > $scheduleEndDateTime) {
            return response()->json(['error' => 'Tidak dapat memesan jadwal yang telah berakhir!'], 422);
        }

        $visitSchedule->is_booked           = false;
        $visitSchedule->is_available        = true;
        $visitSchedule->booked_date         = null;
        $visitSchedule->visitor_id          = null;
        $visitSchedule->visitor_company     = null;
        $visitSchedule->visitor_address     = null;
        $visitSchedule->visitor_purphose    = null;
        $visitSchedule->visitor_contact     = null;
        $visitSchedule->visitor_person      = null;
        $visitSchedule->visitor_note        = null;
        $visitSchedule->save();

        return response()->json(['success' => 'Pembatalan Reservasi Berhasil!']);
    }

    public function RequestTourGuide($id)
    {
        // Cari data jadwal berdasarkan ID
        $visitSchedule = VisitReservation::find($id);

        if (!$visitSchedule) {
            return response()->json(['error' => 'Jadwal Gedung Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal memiliki waktu yang valid untuk diaktifkan
        $currentDateTime = now();
        $scheduleEndDateTime = $visitSchedule->schedule->tanggal . ' ' . $visitSchedule->schedule->end_time;

        if ($visitSchedule->is_booked && $currentDateTime > $scheduleEndDateTime) {
            return response()->json(['error' => 'Tidak dapat mengajukan jadwal yang telah berakhir!'], 422);
        }

        // request tour guide status
        $visitSchedule->tour_guide_requested    = !$visitSchedule->tour_guide_requested;
        $visitSchedule->save();


        if ($visitSchedule->tour_guide_requested) {
            $visitSchedule->humas_id            = $visitSchedule->tour_guide_requested ? Auth::user()->id : null;
            $visitSchedule->tour_guide_req_date = $visitSchedule->tour_guide_requested ? now(): null;
            $visitSchedule->save();
            return response()->json(['success' => 'Tour Guide Berhasil di Ajukan!']);
        } else {
            $visitSchedule->tour_guide_req_date = $visitSchedule->tour_guide_requested ? now(): null;
            $visitSchedule->save();
            return response()->json(['success' => 'Pengajuan Tour Guide Berhasil di batalkan!']);
        }
    }

    public function confirmVisit($id)
    {
        // Cari data jadwal berdasarkan ID
        $visitSchedule = VisitReservation::find($id);

        if (!$visitSchedule) {
            return response()->json(['error' => 'Jadwal Gedung Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal memiliki waktu yang valid untuk diaktifkan
        $currentDateTime = now();
        $scheduleEndDateTime = $visitSchedule->schedule->tanggal . ' ' . $visitSchedule->schedule->end_time;

        if ($visitSchedule->tour_guide_asign && $currentDateTime > $scheduleEndDateTime) {
            return response()->json(['error' => 'Tidak dapat konfirmasi jadwal yang telah berakhir!'], 422);
        }

        // confirm visit status
        $visitSchedule->is_confirm    = !$visitSchedule->is_confirm;
        $visitSchedule->save();


        if ($visitSchedule->is_confirm) {
            $visitSchedule->humas_id            = $visitSchedule->is_confirm ? Auth::user()->id : null;
            $visitSchedule->confirm_date        = $visitSchedule->is_confirm ? now(): null;
            $visitSchedule->save();
            return response()->json(['success' => 'Kunjungan Berhasil di Konfirmasi!']);
        } else {
            $visitSchedule->confirm_date = $visitSchedule->is_confirm ? now(): null;
            $visitSchedule->save();
            return response()->json(['success' => 'Konfirmasi Kunjungan Berhasil di batalkan!']);
        }
    }
}
