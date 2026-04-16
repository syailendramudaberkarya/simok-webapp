<?php

namespace App\Mail;

use App\Models\Anggota;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnggotaDitolak extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Anggota $anggota, public string $alasan)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pemberitahuan Pendaftaran Keanggotaan SiMOK',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.anggota-ditolak',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
