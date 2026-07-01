<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        $user = User::where('email', $request->email)->first();

        return view('frontend.reset-password', [
            'token' => $token,
            'email' => $request->email,
            'user'  => $user
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->remember_token = Str::random(60);
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)]);
        }

        $user = User::where('email', $request->email)->first();
        $username = $user ? $user->username : null;

        return redirect()->route('login')->with([
            'status' => 'Kata sandi berhasil diperbarui!',
            'login_username' => $username,
            'login_email' => $request->email,
        ]);
    }
}
