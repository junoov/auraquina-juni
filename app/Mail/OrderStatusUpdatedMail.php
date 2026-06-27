<?php

namespace App\Mail;

use App\Models\Pesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pesanan $pesanan,
        public string $orderUrl,
        public string $invoiceUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update Pesanan Auraquina #' . $this->pesanan->kode_pesanan,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-updated',
        );
    }
}
