<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\BuildingSchedule;
use App\Models\Building;


use App\Exports\BuildingScheduleExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportBuildingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $perPage = $request->input('per_page', 10);
        $is_available = $request->input('is_available', '');
        $is_booked = $request->input('is_booked', '');

        // Periksa dan update status jadwal yang sudah lewat
        BuildingSchedule::where('tanggal', '<', Carbon::now()->toDateString())
            ->where('is_available', true) // Ganti 'tersedia' dengan status aktif Anda
            ->update(['is_available' => false]); // Ganti 'tidak tersedia' dengan status tidak aktif Anda


        // Ambil data jadwal gedung
        $buildingSchedules = BuildingSchedule::with(['building', 'creator', 'updater', 'humas'])
        ->when($search, function ($query, $search) {
            $query->whereHas('building', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%"); // Filter berdasarkan nama gedung
            })->orWhere('tanggal', 'like', "%{$search}%");
        })
        ->Where('is_available','like', "%{$is_available}%")
        ->Where('is_booked','like', "%{$is_booked}%")
        ->orderBy($sortBy, $order)
        ->paginate($perPage);

        $buildings = Building::isActive()->get();

        if ($request->ajax()) {
            return view('report_building.table', compact('buildingSchedules', 'buildings', 'search', 'sortBy', 'order', 'perPage'));
        }

        return view('report_building.index', compact('buildingSchedules', 'buildings', 'search', 'sortBy', 'order', 'perPage'));
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $is_available = $request->input('is_available', '');
        $is_booked = $request->input('is_booked', '');

        $buildingSchedules = BuildingSchedule::with(['building', 'creator', 'updater', 'humas'])
        ->when($search, function ($query, $search) {
            $query->whereHas('building', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%"); // Filter berdasarkan nama gedung
            })->orWhere('tanggal', 'like', "%{$search}%");
        })
        ->Where('is_available','like', "%{$is_available}%")
        ->Where('is_booked','like', "%{$is_booked}%")
        ->orderBy($sortBy, $order)
        ->get();

        // Panggil export class dengan filter
        return Excel::download(new BuildingScheduleExport($buildingSchedules), 'report_jadwal_gedung.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $is_available = $request->input('is_available', '');
        $is_booked = $request->input('is_booked', '');

        $buildingSchedules = BuildingSchedule::with(['building', 'creator', 'updater', 'humas'])
        ->when($search, function ($query, $search) {
            $query->whereHas('building', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%"); // Filter berdasarkan nama gedung
            })->orWhere('tanggal', 'like', "%{$search}%");
        })
        ->Where('is_available','like', "%{$is_available}%")
        ->Where('is_booked','like', "%{$is_booked}%")
        ->orderBy($sortBy, $order)
        ->get();


        // Render PDF
        $pdf = Pdf::loadView('report_building.pdf', compact('buildingSchedules'))
                    ->setPaper('a4', 'landscape') // Set ukuran kertas ke A4 dan orientasi
                    ->setOptions([
                        'defaultFont' => 'Arial', // Opsional: font default
                        'isHtml5ParserEnabled' => true, // Parser HTML5
                        'isRemoteEnabled' => true // Untuk resource eksternal
                    ]);

        return $pdf->download('Laporan Jadwal Gedung.pdf');
        // return $pdf->stream('Laporan Kunjungan.pdf');
    }
}
