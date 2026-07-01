<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKapal;
use Illuminate\Http\Request;

class MasterKapalController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterKapal::query();

        if ($request->has('search_kapal') && $request->search_kapal != '') {
            $query->where('nama', 'like', '%' . $request->search_kapal . '%');
        }

        $kapals = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json(['kapals' => $kapals]);
        }

        return view('backend.masterdata.kapal.index', compact('kapals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_kapals,nama',
            'tanggal' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        MasterKapal::create([
            'nama' => $request->nama,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Data kapal berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_kapals,nama,' . $id,
            'tanggal' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $kapal = MasterKapal::findOrFail($id);
        $kapal->update([
            'nama' => $request->nama,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Data kapal berhasil diperbarui']);
    }
}
