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
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->addDays(7)->toDateString());
        $is_available = $request->input('is_available', '');
        $is_booked = $request->input('is_booked', '');

        // Periksa dan update status jadwal yang sudah lewat
        BuildingSchedule::where('tanggal', '<', Carbon::now()->toDateString())
            ->where('is_available', true) // Ganti 'tersedia' dengan status aktif Anda
            ->update(['is_available' => false]); // Ganti 'tidak tersedia' dengan status tidak aktif Anda


        // Ambil data jadwal gedung
        $buildingSchedules = BuildingSchedule::with(['building', 'creator', 'updater', 'humas', 'visitReservation'])
        ->when($search, function ($query, $search) {
            $query->whereHas('building', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%"); // Filter berdasarkan nama gedung
            })->orWhere('tanggal', 'like', "%{$search}%");
        })
        ->Where('is_available','like', "%{$is_available}%")
        ->Where('is_booked','like', "%{$is_booked}%")
        ->where('building_id','<>', null)
        ->whereBetween('tanggal', [$fromDate, $toDate])
        ->orderBy($sortBy, $order)
        ->paginate($perPage);

        $buildings = Building::isActive()->get();

        if ($request->ajax()) {
            return view('report_building.table', compact('buildingSchedules', 'buildings', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
        }

        return view('report_building.index', compact('buildingSchedules', 'buildings', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
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

        $buildingSchedules = BuildingSchedule::with(['building', 'creator', 'updater', 'humas', 'visitReservation'])
        ->when($search, function ($query, $search) {
            $query->whereHas('building', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%"); // Filter berdasarkan nama gedung
            })->orWhere('tanggal', 'like', "%{$search}%");
        })
        ->Where('is_available','like', "%{$is_available}%")
        ->Where('is_booked','like', "%{$is_booked}%")
        ->where('building_id','<>', null)
        ->whereBetween('tanggal', [$fromDate, $toDate])
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
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->addDays(7)->toDateString());
        $is_available = $request->input('is_available', '');
        $is_booked = $request->input('is_booked', '');

        $buildingSchedules = BuildingSchedule::with(['building', 'creator', 'updater', 'humas', 'visitReservation'])
        ->when($search, function ($query, $search) {
            $query->whereHas('building', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%"); // Filter berdasarkan nama gedung
            })->orWhere('tanggal', 'like', "%{$search}%");
        })
        ->Where('is_available','like', "%{$is_available}%")
        ->Where('is_booked','like', "%{$is_booked}%")
        ->where('building_id','<>', null)
        ->whereBetween('tanggal', [$fromDate, $toDate])
        ->orderBy($sortBy, $order)
        ->get();

        $imagePath = public_path('images/kop_surat.jpg');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageSrc = 'data:image/png;base64,' . $imageData;

        // Render PDF
        $pdf = Pdf::loadView('report_building.pdf', compact('buildingSchedules', 'imageSrc'))
                    // ->setPaper('a4', 'landscape') // Set ukuran kertas ke A4 dan orientasi
                    ->setPaper('a4', 'potrait') // Set ukuran kertas ke A4 dan orientasi
                    ->setOptions([
                        'defaultFont' => 'Arial', // Opsional: font default
                        'isHtml5ParserEnabled' => true, // Parser HTML5
                        'isRemoteEnabled' => true, // Untuk resource eksternal
                        'isPhpEnabled' => true  // Mengaktifkan PHP jika diperlukan untuk proses dinamis
                    ]);

        // return $pdf->download('Laporan Jadwal Gedung.pdf');
        return $pdf->stream('Laporan Jadwal Gedung.pdf');
    }
}
