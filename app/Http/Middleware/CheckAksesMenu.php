<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\AksesMenu;

class CheckAksesMenu
{
    public function handle($request, Closure $next, $menu)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        if ($user->status !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'username' => 'Maaf, Anda tidak memiliki akses ke website',
            ]);
        }

        $akses = AksesMenu::where('user_id', $user->id)->first();
        if (!$akses) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $raw = $akses->akses_menu;

        if (is_string($raw)) {
            $list = json_decode($raw, true);
            if (!is_array($list))
                $list = [];
        } elseif (is_array($raw)) {
            $list = $raw;
        } else {
            $list = [];
        }

        $list = array_map('strtolower', array_map('trim', $list));
        $menu = strtolower(trim($menu));

        if (!in_array($menu, $list)) {
            abort(403, 'Anda tidak memiliki akses ke menu ini.');
        }

        return $next($request);
    }
}
