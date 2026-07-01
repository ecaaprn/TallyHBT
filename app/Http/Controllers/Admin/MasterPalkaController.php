<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPalka;
use Illuminate\Http\Request;

class MasterPalkaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search_palka;

        $palkas = MasterPalka::when($search, function ($query) use ($search) {
            $query->where('nama', 'like', "%{$search}%");
        })->orderBy('id', 'asc')->paginate(10);

        if ($request->ajax()) {
            return response()->json(['palkas' => $palkas]);
        }

        return view('backend.masterdata.palka.index', compact('palkas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        MasterPalka::create([
            'nama' => $request->nama
        ]);

        return response()->json(['message' => 'Data palka berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $palka = MasterPalka::findOrFail($id);
        $palka->update([
            'nama' => $request->nama
        ]);

        return response()->json(['message' => 'Data palka berhasil diperbarui.']);
    }
}
