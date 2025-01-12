<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TourGuide;
use Illuminate\Support\Facades\Auth;

class TourGuideController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'name');
        $order = $request->input('order', 'asc');
        $perPage = $request->input('per_page', 10);

        // Mengambil data dengan filter, sorting, dan pagination
        $tourGuides = TourGuide::when($search, function ($query, $search) {
                    return $query->where('name', 'like', "%{$search}%");
                })
                ->with(['creator','updater'])
                ->orderBy($sortBy, $order)
                ->paginate($perPage);

        if ($request->ajax()) {
            return view('tour_guide.table', compact('tourGuides', 'search', 'sortBy', 'order', 'perPage'));
        }

        return view('tour_guide.index', compact('tourGuides', 'search', 'sortBy', 'order', 'perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        TourGuide::create([
            'name' => $request->name,
            'is_active' => $request->is_active ?? true,
            'create_by' => Auth::User()->id,
        ]);

        return response()->json(['success' => 'Data Tour Guide Berhasil Di Buat!']);
    }

    public function update(Request $request, $id)
    {
        $tourGuide = TourGuide::find($id);
        if (!$tourGuide) {
            return response()->json(['error' => 'Tour Guide Tidak Di Temukan!']);
        }

        $tourGuide->update([
            'name' => $request->name,
            'is_active' => $request->is_active,
            'update_by' => Auth::User()->id,
        ]);

        return response()->json(['success' => 'Data Tour Guide Berhasil Di Ubah!']);
    }


    public function destroy($id)
    {
        $tourGuide = TourGuide::find($id);

        if ($tourGuide) {
            $tourGuide->delete();
            return response()->json(['success' => 'Data Tour Guide Berhasil Di Hapus!']);
        }

        return response()->json(['error' => 'Tour Guide Tidak Di Temukan!']);
    }

    public function toggleStatus($id)
    {
        $tourGuide = TourGuide::findOrFail($id);
        $tourGuide->is_active = !$tourGuide->is_active; // Toggle status
        $tourGuide->save();

        return response()->json(['error' => 'Status Tour Guide Berhasil Di Perbarui.']);
    }

}
