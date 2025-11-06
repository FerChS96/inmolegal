<?php

namespace App\Mail;

use App\Models\Contrato;
use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PagoEfectivoPendiente extends Mailable
{
    use Queueable, SerializesModels;

    public $contrato;
    public $pago;
    public $checkoutUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Contrato $contrato, Pago $pago, string $checkoutUrl)
    {
        $this->contrato = $contrato;
        $this->pago = $pago;
        $this->checkoutUrl = $checkoutUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago Pendiente - Contrato InmoLegal #' . $this->contrato->token,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pago-efectivo-pendiente',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
