<?php

namespace App\Mail;

use App\Models\SuratPengajuan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SuratStatusChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $suratPengajuan;

    /**
     * Create a new message instance.
     */
    public function __construct(SuratPengajuan $suratPengajuan)
    {
        $this->suratPengajuan = $suratPengajuan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = strtoupper($this->suratPengajuan->status);
        return new Envelope(
            subject: "Update Status Pengajuan Surat [{$statusText}] - {$this->suratPengajuan->nomor_pengajuan}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.surat-status-changed',
            with: [
                'surat' => $this->suratPengajuan,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Hanya attach jika status selesai dan ada file balasan admin
        if ($this->suratPengajuan->status === 'selesai' && $this->suratPengajuan->file_balasan_admin) {
            $attachments[] = Attachment::fromStorageDisk('local', $this->suratPengajuan->file_balasan_admin)
                ->as('Surat_Hasil_Pengajuan_' . $this->suratPengajuan->nomor_pengajuan . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
