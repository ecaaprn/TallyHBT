<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterBooster;
use Illuminate\Http\Request;

class MasterBoosterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search_booster;

        $boosters = MasterBooster::when($search, function ($query) use ($search) {
            $query->where('nama', 'like', "%{$search}%");
        })->orderBy('id', 'asc')->paginate(10);

        if ($request->ajax()) {
            return response()->json(['boosters' => $boosters]);
        }

        return view('backend.masterdata.booster.index', compact('boosters'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        MasterBooster::create([
            'nama' => $request->nama,
        ]);

        return response()->json(['message' => 'Data booster berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $booster = MasterBooster::findOrFail($id);
        $booster->update(['nama' => $request->nama]);

        return response()->json(['message' => 'Data booster berhasil diperbarui.']);
    }
}
