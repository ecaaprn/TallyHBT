<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $access = request()->input('user_cabang_access', [
            'akses_cabang' => 'spesifik',
            'cabang_id' => null
        ]);

        $query = User::query();

        if ($access['akses_cabang'] === 'spesifik' && $access['cabang_id']) {
            $query->whereHas('aksesMenu', function ($q) use ($access) {
                $q->where('cabang_id', $access['cabang_id']);
            });
        }

        if ($request->filled('search_user')) {
            $search = $request->search_user;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'asc')->paginate(10);

        $masterCabang = DB::table('master_cabangs')
            ->select('nama')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                $item->display = $item->nama;
                return $item;
            });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['users' => $users]);
        }

        return view('backend.pengaturan.user.index', compact('users', 'masterCabang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:users,nip',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'group' => 'nullable|string|max:10',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:superadmin,admin,petugas',
            'status' => 'required|string|in:aktif,nonaktif',
            'cabang' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pengaturan-user.index');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'group' => 'nullable|string|max:10',
            'role' => 'required|string|in:superadmin,admin,petugas',
            'status' => 'required|string|in:aktif,nonaktif',
            'cabang' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pengaturan-user.index');
    }

    public function destroy(User $user)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['error' => true], 403);
        }

        return redirect()->route('pengaturan-user.index');
    }
}
