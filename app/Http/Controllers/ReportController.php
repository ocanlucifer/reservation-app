<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitReservation;
use Carbon\Carbon;

use App\Exports\VisitReservationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $perPage = $request->input('per_page', 10);
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->addDays(7)->toDateString());
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
        $visitSchedules = VisitReservation::with(['schedule', 'creator', 'updater', 'humas', 'visitor', 'koordinator'])
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
        ->where('schedule.building_id','<>', null)
        ->where('schedule.is_internal', false)
        ->whereBetween('schedule.tanggal', [$fromDate, $toDate])
        ->orderBy($sortBy, $order)
        ->select('visit_reservations.*')
        ->paginate($perPage);

        if ($request->ajax()) {
            return view('report.table', compact('visitSchedules', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
        }

        return view('report.index', compact('visitSchedules', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->addDays(7)->toDateString());
        $is_available = $request->input('is_available', '');
        $is_booked = $request->input('is_booked', '');
        $tg_req = $request->input('t_g_req', '');
        $tg_assign = $request->input('t_g_assign', '');
        $is_confirm = $request->input('is_confirm', '');

        if ($sortBy == 'tanggal') {
            $sortBy = 'schedule.tanggal';
        }

        $visitSchedules = VisitReservation::with(['schedule', 'creator', 'updater', 'humas', 'visitor', 'koordinator'])
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
        ->whereBetween('schedule.tanggal', [$fromDate, $toDate])
        ->where('schedule.building_id','<>', null)
        ->where('schedule.is_internal', false)
        ->orderBy($sortBy, $order)
        ->select('visit_reservations.*')
        ->get();

        // Panggil export class dengan filter
        return Excel::download(new VisitReservationsExport($visitSchedules), 'report_kunjungan.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->addDays(7)->toDateString());
        $is_available = $request->input('is_available', '');
        $is_booked = $request->input('is_booked', '');
        $tg_req = $request->input('t_g_req', '');
        $tg_assign = $request->input('t_g_assign', '');
        $is_confirm = $request->input('is_confirm', '');

        if ($sortBy == 'tanggal') {
            $sortBy = 'schedule.tanggal';
        }

        $visitSchedules = VisitReservation::with(['schedule', 'creator', 'updater', 'humas', 'visitor', 'koordinator'])
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
        ->whereBetween('schedule.tanggal', [$fromDate, $toDate])
        ->where('schedule.building_id','<>', null)
        ->where('schedule.is_internal', false)
        ->orderBy($sortBy, $order)
        ->select('visit_reservations.*')
        ->get();


        // Render PDF
        $pdf = Pdf::loadView('report.pdf', compact('visitSchedules'))
                    ->setPaper('a4', 'landscape') // Set ukuran kertas ke A4 dan orientasi
                    ->setOptions([
                        'defaultFont' => 'Arial', // Opsional: font default
                        'isHtml5ParserEnabled' => true, // Parser HTML5
                        'isRemoteEnabled' => true // Untuk resource eksternal
                    ]);

        return $pdf->download('Laporan Kunjungan.pdf');
        // return $pdf->stream('Laporan Kunjungan.pdf');
    }
}
