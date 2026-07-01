<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterTruck;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterTruckController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterTruck::query();

        if ($request->filled('search_truck')) {
            $query->where('nama', 'like', '%' . $request->search_truck . '%');
        }

        $trucks = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'trucks' => $trucks
            ]);
        }

        return view('backend.masterdata.truck.index', compact('trucks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_trucks,nama',
        ]);

        MasterTruck::create([
            'nama' => $request->nama,
        ]);

        return response()->json([
            'success' => 'Data truck berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, MasterTruck $truck)
    {
        $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('master_trucks', 'nama')->ignore($truck->id),
            ],
        ]);

        $truck->update([
            'nama' => $request->nama,
        ]);

        return response()->json([
            'success' => 'Data truck berhasil diperbarui'
        ]);
    }

    public function destroy(MasterTruck $truck)
    {
        return response()->json([
            'error' => 'Fungsi hapus data tidak diizinkan'
        ], 403);
    }
}
