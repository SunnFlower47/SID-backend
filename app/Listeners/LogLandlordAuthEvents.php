<?php

namespace App\Listeners;

use App\Models\Central\LandlordAuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogLandlordAuthEvents
{
    /**
     * Tangani semua event auth yang berkaitan dengan guard 'landlord'.
     * Listener tunggal ini menangani Login, Logout, dan Failed.
     */
    public function handle(Login|Logout|Failed $event): void
    {
        // Hanya proses event dari guard 'landlord'
        if ($event->guard !== 'landlord') {
            return;
        }

        match (true) {
            $event instanceof Login  => $this->handleLogin($event),
            $event instanceof Logout => $this->handleLogout($event),
            $event instanceof Failed => $this->handleFailed($event),
        };
    }

    private function handleLogin(Login $event): void
    {
        LandlordAuditLog::record(
            event: 'login_success',
            description: "Login berhasil sebagai {$event->user->email}",
            metadata: ['remember' => $event->remember]
        );
    }

    private function handleLogout(Logout $event): void
    {
        LandlordAuditLog::record(
            event: 'logout',
            description: "Logout dari sesi landlord"
        );
    }

    private function handleFailed(Failed $event): void
    {
        // Catat email yang gagal login meski user tidak ditemukan
        $email = $event->credentials['email'] ?? 'unknown';

        LandlordAuditLog::create([
            'event'       => 'login_failed',
            'actor_email' => $email,
            'actor_id'    => $event->user?->id,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'description' => "Percobaan login gagal untuk email: {$email}",
            'metadata'    => null,
        ]);
    }
}
