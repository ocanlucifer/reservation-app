<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\VisitReservation;
use App\Models\BuildingSchedule;
use App\Models\TourGuide;

class AssignTourGuideController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $perPage = $request->input('per_page', 10);
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->addDays(7)->toDateString());
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
        ->Where('visit_reservations.tour_guide_requested',true)
        // ->Where('visit_reservations.is_confirm',false)
        ->Where('visit_reservations.tour_guide_assign','like', "%{$tg_assign}%")
        ->Where('visit_reservations.is_confirm','like', "%{$is_confirm}%")
        ->whereBetween('schedule.tanggal', [$fromDate, $toDate])
        ->orderBy($sortBy, $order)
        ->select('visit_reservations.*')
        ->paginate($perPage);

        $tourguides = TourGuide::isActive()->get();

        if ($request->ajax()) {
            return view('tour_guide_assign.table', compact('visitSchedules', 'tourguides', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
        }

        return view('tour_guide_assign.index', compact('visitSchedules', 'tourguides', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
    }

    public function update(Request $request, $id)
    {
        $visitSchedule = VisitReservation::find($id);
        if (!$visitSchedule) {
            return response()->json(['error' => 'Jadwal Gedung Tidak Ditemukan!'], 404);
        }

        // Update data
        $visitSchedule->update([
            'tour_guide_id'             => $request->tour_guide_id,
            'tour_guide_assign'         => true,
            'tour_guide_assign_date'    => now(),
            'end_time'                  => $request->end_time,
            'koordinator_id'            => Auth::user()->id,
        ]);

        return response()->json(['success' => 'Data Tour Guide Berhasil di Simpan!']);
    }

    public function destroy($id)
    {
        // Cari data jadwal berdasarkan ID
        $visitSchedule = VisitReservation::find($id);

        if (!$visitSchedule) {
            return response()->json(['error' => 'Jadwal Kunjungan Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal tidak memiliki reservasi aktif
        if ($visitSchedule->is_confirm) {
            return response()->json(['error' => 'Tour Guide tidak dapat di batalkan karena sudah terkonfirmasi!'], 422);
        }

        // batalkan tour guide
        $visitSchedule->tour_guide_assign       = false;
        $visitSchedule->tour_guide_assign_date  = null;
        $visitSchedule->koordinator_id          = null;
        $visitSchedule->save();

        return response()->json(['success' => 'Data Jadwal Kunjungan Berhasil Dihapus!']);
    }
}
