<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterHose;
use Illuminate\Http\Request;

class MasterHoseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search_hose;

        $hoses = MasterHose::when($search, function ($query) use ($search) {
            $query->where('nama', 'like', "%{$search}%");
        })->orderBy('id', 'asc')->paginate(10);

        if ($request->ajax()) {
            return response()->json(['hoses' => $hoses]);
        }

        return view('backend.masterdata.hose.index', compact('hoses'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        MasterHose::create([
            'nama' => $request->nama,
        ]);

        return response()->json(['message' => 'Data hose berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $hose = MasterHose::findOrFail($id);
        $hose->update(['nama' => $request->nama]);

        return response()->json(['message' => 'Data hose berhasil diperbarui.']);
    }
}
