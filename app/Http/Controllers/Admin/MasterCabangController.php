<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterCabang;
use Illuminate\Http\Request;

class MasterCabangController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterCabang::query();

        if ($request->filled('search_cabang')) {
            $query->where('nama', 'like', '%' . $request->search_cabang . '%');
        }

        $cabangs = $query->orderBy('created_at', 'asc')->paginate(10);

        if ($request->ajax()) {
            return response()->json(['cabangs' => $cabangs]);
        }

        return view('backend.masterdata.cabang.index', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_cabangs,nama',
        ]);

        MasterCabang::create([
            'nama' => $request->nama
        ]);

        return response()->json(['success' => 'Cabang berhasil ditambahkan']);
    }

    public function update(Request $request, MasterCabang $cabang)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_cabangs,nama,' . $cabang->id,
        ]);

        $cabang->update([
            'nama' => $request->nama
        ]);

        return response()->json(['success' => 'Cabang berhasil diperbarui']);
    }
}
