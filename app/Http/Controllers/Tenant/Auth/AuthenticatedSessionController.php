<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): \Inertia\Response
    {
        // Fallback defensive: pastikan csp_nonce selalu tersedia walau middleware tidak terpasang
        $nonce = $request->attributes->get('csp_nonce') ?? base64_encode(random_bytes(16));

        return \Inertia\Inertia::render('Auth/Login', [
            'csp_nonce' => $nonce,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // session()->regenerate() hanya mengganti session ID, TIDAK menghapus data.
        // tenant_id sudah disimpan di session setelah Auth::login() di LoginRequest::authenticate().
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        try {
            Log::info('Logout attempt', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user() ? Auth::user()->name : 'No user',
                'user_role' => Auth::user() ? Auth::user()->roles->pluck('name')->toArray() : 'No roles',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Force logout regardless of user state
            if (Auth::check()) {
                Auth::guard('web')->logout();
            }

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('Logout successful');
            session()->flash('success', 'Anda telah berhasil logout');
            return redirect('/');

        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());

            // Force logout even if there's an error
            if (Auth::check()) {
                Auth::guard('web')->logout();
            }
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        }
    }
}
