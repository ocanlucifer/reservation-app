<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\VisitReservation;
use App\Models\BuildingSchedule;

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

}
