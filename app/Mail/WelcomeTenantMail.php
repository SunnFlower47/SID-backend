<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeTenantMail extends Mailable
{
    use Queueable, SerializesModels;

    public Tenant $tenant;
    public string $loginUrl;
    public string $publicUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        
        $baseDomain = \App\Models\Central\CentralSetting::get('central_base_domain', 'sistem-desa-cibatu.test');
        $adminDomain = \App\Models\Central\CentralSetting::get('central_admin_domain', 'admin.sistem-desa-cibatu.test');

        // Website Desa (Warga Subdomain)
        $publicDomain = $tenant->domains->first()->domain ?? ($tenant->id . '.' . $baseDomain);
        
        $scheme = request()->secure() ? 'https://' : 'http://';
        
        $this->publicUrl = $scheme . $publicDomain;
        $this->loginUrl = $scheme . $adminDomain . '/login';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Tenant Berhasil - Akses Admin Panel Desa ' . $this->tenant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-tenant',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
