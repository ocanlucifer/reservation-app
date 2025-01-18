<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\VisitReservation;
use App\Models\BuildingSchedule;
use App\Models\Building;

class VisitReservationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $perPage = $request->input('per_page', 10);
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->addDays(7)->toDateString());
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
        ->Where('visit_reservations.is_booked',true)
        ->Where('visit_reservations.tour_guide_requested','like', "%{$tg_req}%")
        ->Where('visit_reservations.tour_guide_assign','like', "%{$tg_assign}%")
        ->Where('visit_reservations.is_confirm','like', "%{$is_confirm}%")
        ->whereBetween('schedule.tanggal', [$fromDate, $toDate])
        ->where('visitor_id', Auth::user()->id)
        ->orderBy($sortBy, $order)
        ->select('visit_reservations.*')
        ->paginate($perPage);


        if ($request->ajax()) {
            return view('visit_reservation.table', compact('visitSchedules', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
        }

        return view('visit_reservation.index', compact('visitSchedules', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
    }

    public function getSchedules(Request $request)
    {
        // $schedules = BuildingSchedule::with('building')
        //     ->whereBetween('tanggal', [$request->start_date, $request->end_date])
        //     ->get();

        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->addDays(7)->toDateString());
        $building_id = $request->input('gedung_id');

        // Ambil data jadwal gedung
        $schedules = VisitReservation::with(['schedule', 'creator', 'updater', 'humas', 'visitor', 'koordinator', 'tourGuide'])
        ->join('building_schedules as schedule', 'visit_reservations.building_schedule_id', '=', 'schedule.id') // Gabung ke tabel schedule
        ->whereBetween('schedule.tanggal', [$fromDate, $toDate])
        ->when($building_id, function ($query, $building_id) {
            return $query->where('schedule.building_id', $building_id);
        })
        ->select('visit_reservations.*')
        ->get();

        $events = $schedules->map(function ($schedule) {
            $building_name = $schedule->schedule->building_id ? $schedule->schedule->building->name : 'Belum di pilihkan Gedung';
            $is_booked = $schedule->schedule->is_booked;
            $is_requested = $schedule->tour_guide_requested;
            $is_assign = $schedule->tour_guide_assign;
            $is_confirm = $schedule->is_confirm;

            if ($is_confirm){
                $color = 'green';
            } else if ($is_booked and !$is_requested){
                $color = 'orange';
            } else if ($is_requested and !$is_assign){
                $color = 'purple';
            } else if($is_assign and !$is_confirm) {
                $color = 'aqua';
            } else {
                $color = 'red';
            }
            return [
                'id' => $schedule->schedule->id,
                'title' => $schedule->visitor_company.' - '.$schedule->visitor_person.' orang ('.$building_name.')' ,
                'building_name' => $building_name,
                'start' => $schedule->schedule->tanggal . 'T' . \Carbon\Carbon::parse($schedule->schedule->start_time)->format('H:i'),
                'end' => $schedule->schedule->tanggal . 'T' . \Carbon\Carbon::parse($schedule->schedule->end_time)->format('H:i'),
                'color' => $color,
                'start_time' => \Carbon\Carbon::parse($schedule->schedule->start_time)->format('H:i'),
                'end_time'  => \Carbon\Carbon::parse($schedule->schedule->end_time)->format('H:i'),
                'is_available' => $schedule->schedule->is_available,
                'is_booked' => $schedule->schedule->is_booked,
                'tanggal'   => $schedule->schedule->tanggal,
                'building_id' => $schedule->schedule->building_id,
                'company'   => $schedule->visitor_company,
                'address'   => $schedule->visitor_address,
                'kegiatan'   => $schedule->visitor_purphose,
                'contact'    => $schedule->visitor_contact,
                'peserta'   => $schedule->visitor_person,
                'note'      => $schedule->visitor_note,
                'visit_id'  => $schedule->id,
                'is_owner'  => $schedule->create_by == Auth::user()->id ? true : false,
            ];
        });

        return response()->json($events);
    }

    public function calendar()
    {
        // Periksa dan update status jadwal yang sudah lewat
        BuildingSchedule::where('tanggal', '<', Carbon::now()->toDateString())
            ->where('is_available', true) // Ganti 'tersedia' dengan status aktif Anda
            ->update(['is_available' => false]); // Ganti 'tidak tersedia' dengan status tidak aktif Anda
        $buildings = Building::isActive()->get();
        return view('visit_reservation.calendar', compact('buildings'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'start_time' => Carbon::parse($request->start_time)->format('H:i'),
            'end_time'   => Carbon::parse($request->end_time)->format('H:i'),
        ]);

        // Validasi request
        $request->validate([
            // 'building_id'  => 'required|exists:buildings,id',
            'tanggal'      => 'required|date',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'is_available' => 'nullable|boolean',
        ]);

        // Cek konflik jadwal
        $conflict = BuildingSchedule::where('building_id', $request->building_id)
            ->where('tanggal', $request->tanggal)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Jadwal bertabrakan dengan jadwal lain!'], 422);
        }

        // Simpan data
        $buildingSchedule = BuildingSchedule::create([
            // 'building_id'   => $request->building_id,
            'tanggal'       => $request->tanggal,
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
            'is_available'  => true, //$request->is_available ?? true,
            'is_booked'     => false,
            // 'booked_date'   => now(),
            // 'humas_id'      => Auth::user()->id,
            'create_by'     => Auth::user()->id,
        ]);

        if ($buildingSchedule) {
            VisitReservation::create([
                'building_schedule_id'  => $buildingSchedule->id,
                // 'humas_id'              => Auth::user()->id,
                'create_by'             => Auth::user()->id,
                'visitor_id'            => Auth::user()->id,
                'is_available'          => false,
                'is_booked'             => true,
                'booked_date'           => now(),
                'visitor_id'            => Auth::user()->id,
                'visitor_company'       => $request->visitor_company,
                'visitor_address'       => $request->visitor_address,
                'visitor_purphose'      => $request->visitor_purphose,
                'visitor_contact'       => $request->visitor_contact,
                'visitor_person'        => $request->visitor_person,
                'visitor_note'          => $request->visitor_note,
                // 'tour_guide_requested'  => true,
                // 'tour_guide_req_date'   => now(),
                // 'tour_guide_assign'     => true,
                // 'tour_guide_assign_date'=> now(),
                // 'is_confirm'            => true,
                // 'confirm_date'          => now(),
            ]);

        }

        return response()->json(['success' => 'Data Jadwal Gedung Berhasil Dibuat!']);
    }

    public function update(Request $request, $id)
    {
        $visit_id = $request->visit_id;
        $buildingSchedule = BuildingSchedule::find($id);
        if (!$buildingSchedule) {
            return response()->json(['error' => 'Jadwal Gedung Tidak Ditemukan!'], 404);
        }

        $request->merge([
            'start_time' => Carbon::parse($request->start_time)->format('H:i'),
            'end_time'   => Carbon::parse($request->end_time)->format('H:i'),
        ]);

        try {
            $request->validate([
                'tanggal'      => 'required|date',
                'start_time'   => 'required|date_format:H:i',
                'end_time'     => 'required|date_format:H:i|after:start_time',
                'is_available' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'periksa kembali data yang ada masukan'], 422); // Kirim semua error validasi
        }

        // Cek konflik jadwal (abaikan jadwal saat ini)
        $conflict = BuildingSchedule::where('building_id', $request->building_id)
            ->where('tanggal', $request->tanggal)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Jadwal bertabrakan dengan jadwal lain!'], 422);
        }

        // Update data
        $buildingSchedule->update([
            'building_id'   => $request->building_id,
            'tanggal'       => $request->tanggal,
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
            'update_by'     => Auth::user()->id,
        ]);

        $visitReservation = VisitReservation::find($visit_id);
        if ($visitReservation){
            $visitReservation->update([
                'visitor_company'       => $request->visitor_company,
                'visitor_address'       => $request->visitor_address,
                'visitor_purphose'      => $request->visitor_purphose,
                'visitor_contact'       => $request->visitor_contact,
                'visitor_person'        => $request->visitor_person,
                'visitor_note'          => $request->visitor_note,
            ]);
        }


        return response()->json(['success' => 'Data Jadwal Gedung Berhasil Diubah!']);
    }

    public function destroy($id)
    {
        // Cari data jadwal berdasarkan ID
        $buildingSchedule = BuildingSchedule::find($id);

        if (!$buildingSchedule) {
            return response()->json(['error' => 'Jadwal Gedung Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal tidak memiliki reservasi aktif
        // if ($buildingSchedule->visitReservation()->exists()) {
        //     return response()->json(['error' => 'Jadwal tidak dapat dihapus karena memiliki reservasi aktif!'], 422);
        // }

        // Hapus data jadwal
        $buildingSchedule->delete();

        return response()->json(['success' => 'Data Jadwal Gedung Berhasil dibatalkan!']);
    }

}
