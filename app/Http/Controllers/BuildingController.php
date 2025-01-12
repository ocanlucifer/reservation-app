<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Building;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'name');
        $order = $request->input('order', 'asc');
        $perPage = $request->input('per_page', 10);

        // Mengambil data dengan filter, sorting, dan pagination
        $buildings = Building::when($search, function ($query, $search) {
                    return $query->where('name', 'like', "%{$search}%");
                })
                ->with(['creator','updater'])
                ->orderBy($sortBy, $order)
                ->paginate($perPage);

        if ($request->ajax()) {
            return view('building.table', compact('buildings', 'search', 'sortBy', 'order', 'perPage'));
        }

        return view('building.index', compact('buildings', 'search', 'sortBy', 'order', 'perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Building::create([
            'name' => $request->name,
            'is_active' => $request->is_active ?? true,
            'create_by' => Auth::User()->id,
        ]);

        return response()->json(['success' => 'Data Gedung Berhasil Di Buat!']);
    }

    public function update(Request $request, $id)
    {
        $building = Building::find($id);
        if (!$building) {
            return response()->json(['error' => 'Gedung Tidak Di Temukan!']);
        }

        $building->update([
            'name' => $request->name,
            'is_active' => $request->is_active,
            'update_by' => Auth::User()->id,
        ]);

        return response()->json(['success' => 'Data Gedung Berhasil Di Ubah!']);
    }


    public function destroy($id)
    {
        $building = Building::find($id);

        if ($building) {
            $building->delete();
            return response()->json(['success' => 'Data Gedung Berhasil Di Hapus!']);
        }

        return response()->json(['error' => 'Gedung Tidak Di Temukan!']);
    }

    public function toggleStatus($id)
    {
        $building = Building::findOrFail($id);
        $building->is_active = !$building->is_active; // Toggle status
        $building->save();

        return response()->json(['error' => 'Status Gedung Berhasil Di Perbarui.']);
    }

}
