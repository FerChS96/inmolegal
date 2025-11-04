<?php

namespace App\Mail;

use App\Models\Contrato;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class ContratoGenerado extends Mailable
{
    use Queueable, SerializesModels;

    public $contrato;
    public $pdfRecibo;
    public $pdfContrato;

    /**
     * Create a new message instance.
     */
    public function __construct(Contrato $contrato, $pdfRecibo, $pdfContrato)
    {
        $this->contrato = $contrato;
        $this->pdfRecibo = $pdfRecibo;
        $this->pdfContrato = $pdfContrato;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contrato de Arrendamiento - InmoLegal #' . $this->contrato->token,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contrato-generado',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfRecibo, 'recibo-' . $this->contrato->token . '.pdf')
                ->withMime('application/pdf'),
            Attachment::fromData(fn () => $this->pdfContrato, 'contrato-' . $this->contrato->token . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
