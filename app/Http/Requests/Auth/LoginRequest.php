<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // [SaaS] Cari tenant_id berdasarkan email user di tabel central
        $map = \App\Models\Central\UserTenantMap::with('tenant')->where('email', $this->input('email'))->first();

        if ($map) {
            if (!$map->tenant) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => 'Desa untuk akun Anda tidak ditemukan.',
                ]);
            }

            if (isset($map->tenant->is_active) && !$map->tenant->is_active) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => 'Desa Anda sedang dinonaktifkan. Silakan hubungi admin Diskominfo.',
                ]);
            }

            // Initialize tenancy agar model User baca dari database tenant yang benar
            tenancy()->initialize($map->tenant_id);
        } else {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'Akun tidak terdaftar di desa manapun.',
            ]);
        }

        // [SaaS] Cari user secara manual setelah tenancy diinisialisasi
        $user = \App\Models\User::where('email', $this->input('email'))->first();

        if (! $user || ! \Illuminate\Support\Facades\Hash::check($this->input('password'), $user->password)) {
            Log::warning('Failed login attempt', [
                'email' => $this->input('email'),
                'tenant' => $map->tenant_id,
                'ip' => $this->ip(),
                'user_agent' => $this->userAgent(),
                'timestamp' => now(),
            ]);

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // [KRUSIAL] Auth::login() secara internal memanggil session->migrate(true)
        // yang MENGHAPUS semua session data termasuk 'tenant_id'.
        // Karena itu, kita simpan tenant_id KE SESSION SETELAH Auth::login() selesai.
        Auth::login($user, $this->boolean('remember'));

        // Simpan tenant_id ke session SETELAH Auth::login()
        // agar tidak terhapus oleh session->migrate(true) internal Auth::login()
        session()->put('tenant_id', $map->tenant_id);

        Log::info('Successful login', [
            'email' => $this->input('email'),
            'tenant' => $map->tenant_id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
        ]);

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
