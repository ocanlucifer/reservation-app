<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\BuildingSchedule;
use App\Models\Building;
use App\Models\VisitReservation;

class BuildingScheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->addDays(7)->toDateString());
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
        ->whereBetween('tanggal', [$fromDate, $toDate])
        ->Where('is_available','like', "%{$is_available}%")
        ->Where('is_booked','like', "%{$is_booked}%")
        ->orderBy($sortBy, $order)
        ->paginate($perPage);

        $buildings = Building::isActive()->get();

        if ($request->ajax()) {
            return view('building_schedule.table', compact('buildingSchedules', 'buildings', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
        }

        return view('building_schedule.index', compact('buildingSchedules', 'buildings', 'search', 'sortBy', 'order', 'perPage', 'fromDate', 'toDate'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'start_time' => Carbon::parse($request->start_time)->format('H:i'),
            'end_time'   => Carbon::parse($request->end_time)->format('H:i'),
        ]);

        // Validasi request
        $request->validate([
            'building_id'  => 'required|exists:buildings,id',
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
        BuildingSchedule::create([
            'building_id'   => $request->building_id,
            'tanggal'       => $request->tanggal,
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
            'is_available'  => $request->is_available ?? true,
            'create_by'     => Auth::user()->id,
        ]);

        return response()->json(['success' => 'Data Jadwal Gedung Berhasil Dibuat!']);
    }

    public function update(Request $request, $id)
    {
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
                'building_id'  => 'required|exists:buildings,id',
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
            'is_available'  => $request->is_available,
            'update_by'     => Auth::user()->id,
        ]);

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
        if ($buildingSchedule->visitReservation()->exists()) {
            return response()->json(['error' => 'Jadwal tidak dapat dihapus karena memiliki reservasi aktif!'], 422);
        }

        // Hapus data jadwal
        $buildingSchedule->delete();

        return response()->json(['success' => 'Data Jadwal Gedung Berhasil Dihapus!']);
    }

    public function toggleStatus($id)
    {
        // Cari data jadwal berdasarkan ID
        $buildingSchedule = BuildingSchedule::find($id);

        if (!$buildingSchedule) {
            return response()->json(['error' => 'Jadwal Gedung Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal memiliki waktu yang valid untuk diaktifkan
        $currentDateTime = now();
        $scheduleEndDateTime = $buildingSchedule->tanggal . ' ' . $buildingSchedule->end_time;

        if ($buildingSchedule->is_available && $currentDateTime > $scheduleEndDateTime) {
            return response()->json(['error' => 'Tidak dapat mengaktifkan jadwal yang telah berakhir!'], 422);
        }

        // Toggle status
        $buildingSchedule->is_available = !$buildingSchedule->is_available;
        $buildingSchedule->save();

        return response()->json(['success' => 'Status Jadwal Gedung Berhasil Diperbarui!']);
    }

    public function booking($id)
    {
        // Cari data jadwal berdasarkan ID
        $buildingSchedule = BuildingSchedule::find($id);

        if (!$buildingSchedule) {
            return response()->json(['error' => 'Jadwal Gedung Tidak Ditemukan!'], 404);
        }

        // Tambahkan validasi: Pastikan jadwal memiliki waktu yang valid untuk diaktifkan
        $currentDateTime = now();
        $scheduleEndDateTime = $buildingSchedule->tanggal . ' ' . $buildingSchedule->end_time;

        if ($buildingSchedule->is_booked && $currentDateTime > $scheduleEndDateTime) {
            return response()->json(['error' => 'Tidak dapat memesan jadwal yang telah berakhir!'], 422);
        }

        // Toggle status
        $buildingSchedule->is_booked    = !$buildingSchedule->is_booked;
        $buildingSchedule->is_available = !$buildingSchedule->is_available;
        $buildingSchedule->save();


        $buildingSchedule->humas_id     = $buildingSchedule->is_booked ? Auth::user()->id : null;
        $buildingSchedule->booked_date  = $buildingSchedule->is_booked ? now(): null;
        $buildingSchedule->save();

        if ($buildingSchedule->is_booked) {
            VisitReservation::create([
                'building_schedule_id'  => $buildingSchedule->id,
                'humas_id'              => Auth::user()->id,
                'create_by'             => Auth::user()->id,
            ]);
        }

        return response()->json(['success' => 'Status Jadwal Gedung Berhasil di Pesan!']);
    }


}
