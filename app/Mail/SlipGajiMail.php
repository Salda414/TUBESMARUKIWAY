<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Penggajian;
use Barryvdh\DomPDF\Facade\Pdf;

class SlipGajiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $penggajian;

    public function __construct(Penggajian $penggajian)
    {
        $this->penggajian = $penggajian;
    }

    public function build()
    {
        $pdf = Pdf::loadView('pdf.slipgaji', ['penggajian' => $this->penggajian]);

        return $this->subject('Slip Gaji')
            ->view('emails.slipgaji')
            ->attachData($pdf->output(), 'slip_gaji.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}

