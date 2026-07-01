<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\AksesMenu;
use App\Models\MasterCabang;

class CheckAksesCabang
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            $request->merge([
                'user_cabang_access' => [
                    'akses_cabang' => 'spesifik',
                    'cabang_id' => null,
                    'cabang_nama' => null
                ]
            ]);
            return $next($request);
        }

        $aksesMenu = AksesMenu::where('user_id', $user->id)->first();

        if (!$aksesMenu) {
            $request->merge([
                'user_cabang_access' => [
                    'akses_cabang' => 'spesifik',
                    'cabang_id' => null,
                    'cabang_nama' => $user->cabang
                ]
            ]);
            return $next($request);
        }

        $cabangNama = null;
        if ($aksesMenu->cabang_id) {
            $cabang = MasterCabang::find($aksesMenu->cabang_id);
            $cabangNama = $cabang ? $cabang->nama : $user->cabang;
        } else {
            $cabangNama = $user->cabang;
        }

        $request->merge([
            'user_cabang_access' => [
                'akses_cabang' => $aksesMenu->akses_cabang,
                'cabang_id' => $aksesMenu->cabang_id,
                'cabang_nama' => $cabangNama
            ]
        ]);

        return $next($request);
    }
}
