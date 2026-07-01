<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MasterCabang;
use App\Models\AksesMenu;
use Illuminate\Http\Request;

class AksesMenuController extends Controller
{
public function index(Request $request)
    {
        $search = $request->input('search');
        $query = AksesMenu::with(['user', 'cabang']);

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        $akses = $query->orderBy('created_at', 'asc')->paginate(10);

        if ($request->ajax() || $request->input('ajax') == 1) {
            return response()->json([
                'akses_menu' => $akses
            ]);
        }

        $users = User::orderBy('id', 'asc')->get();
        $cabangs = MasterCabang::orderBy('nama', 'asc')->get();

        return view('backend.pengaturan.akses-menu.index', compact('akses', 'users', 'cabangs'));
    }

    public function getUsers()
    {
        $access = request()->input('user_cabang_access', [
            'akses_cabang' => 'spesifik',
            'cabang_id' => null
        ]);

        $query = User::select('id', 'nama', 'cabang', 'role', 'status')
            ->whereNotNull('nama');

        if ($access['akses_cabang'] === 'spesifik' && $access['cabang_id']) {
            $query->whereHas('aksesMenu', function ($q) use ($access) {
                $q->where('cabang_id', $access['cabang_id']);
            });
        }

        $users = $query->orderBy('id', 'asc')->get();

        return response()->json(['users' => $users]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'akses_cabang' => 'required|in:semua,spesifik',
            'cabang_id'    => 'nullable|exists:master_cabangs,id',
            'akses_menu'   => 'required|array'
        ]);

        $user = User::findOrFail($validated['user_id']);

        $cabangId = $validated['cabang_id'];
        if ($validated['akses_cabang'] === 'spesifik' && !$cabangId) {
            $cabang = MasterCabang::where('nama', $user->cabang)->first();
            $cabangId = $cabang ? $cabang->id : null;
        }

        AksesMenu::updateOrCreate(
            ['user_id' => $user->id],
            [
                'cabang_id'    => $cabangId,
                'akses_cabang' => $validated['akses_cabang'],
                'akses_menu'   => $validated['akses_menu']
            ]
        );

        $user->update([
            'role' => $this->generateRoleFromMenu($validated['akses_menu'])
        ]);

        $aksesMenuModel = AksesMenu::where('user_id', $user->id)
            ->with(['user', 'cabang'])
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'aksesMenu' => $aksesMenuModel
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'akses_menu'   => 'required|array',
            'akses_cabang' => 'required|in:semua,spesifik',
            'cabang_id'    => 'nullable|exists:master_cabangs,id'
        ]);

        $aksesMenuModel = AksesMenu::findOrFail($id);

        $aksesMenuModel->update([
            'akses_menu'   => $validated['akses_menu'],
            'akses_cabang' => $validated['akses_cabang'],
            'cabang_id'    => $validated['cabang_id']
        ]);

        if ($aksesMenuModel->user) {
            $aksesMenuModel->user->update([
                'role' => $this->generateRoleFromMenu($validated['akses_menu'])
            ]);
        }

        $aksesMenuModel->load(['user', 'cabang']);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui',
            'aksesMenu' => $aksesMenuModel
        ]);
    }

    private function generateRoleFromMenu(array $menus)
    {
        $count = count($menus);

        return match (true) {
            $count >= 3 => 'superadmin',
            $count === 2 => 'admin',
            $count === 1 => 'petugas',
            default => 'user'
        };
    }
}
