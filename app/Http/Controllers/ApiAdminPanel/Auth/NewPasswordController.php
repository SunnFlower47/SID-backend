<?php

namespace App\Http\Controllers\ApiAdminPanel\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($request->wantsJson()) {
            if ($status == Password::PASSWORD_RESET) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password berhasil direset!'
                ]);
            } else {
                $errorMessage = match($status) {
                    Password::INVALID_TOKEN => 'Token reset password tidak valid atau sudah digunakan.',
                    Password::INVALID_USER => 'Email tidak ditemukan.',
                    Password::THROTTLED => 'Terlalu banyak percobaan. Silakan coba lagi nanti.',
                    default => 'Terjadi kesalahan saat reset password.'
                };
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage
                ], 422);
            }
        }

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
        } else {
            $errorMessage = match($status) {
                Password::INVALID_TOKEN => 'Token reset password tidak valid atau sudah digunakan.',
                Password::INVALID_USER => 'Email tidak ditemukan.',
                Password::THROTTLED => 'Terlalu banyak percobaan. Silakan coba lagi nanti.',
                default => 'Terjadi kesalahan saat reset password. Silakan coba lagi.'
            };
            
            return back()->withInput($request->only('email'))
                        ->withErrors(['email' => $errorMessage]);
        }
    }
}
