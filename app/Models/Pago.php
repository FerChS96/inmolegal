<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $primaryKey = 'idpago';

    protected $fillable = [
        'idcontrato',
        'payment_request_id',
        'checkout_url',
        'clip_payment_id',
        'clip_order_id',
        'amount',
        'currency',
        'fee_amount',
        'net_amount',
        'status',
        'payment_method',
        // Campos de tarjeta removidos por falta de certificación PCI DSS
        // 'card_brand', 'card_last4', 'card_type', 'card_issuer'
        'payment_created_at',
        'payment_expires_at',
        'paid_at',
        'clip_checkout_response',
        'clip_payment_response',
        'clip_transaction_details',
        'webhook_received',
        'webhook_data',
        'webhook_received_at',
        'webhook_attempts',
        'customer_name',
        'customer_email',
        'customer_phone',
        'refunded',
        'refund_id',
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'description',
        'metadata',
        'error_code',
        'error_message',
        'attempts',
        'ip_address',
        'user_agent',
        'processed',
        'notification_sent',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'clip_checkout_response' => 'array',
        'clip_payment_response' => 'array',
        'clip_transaction_details' => 'array',
        'webhook_data' => 'array',
        'metadata' => 'array',
        'webhook_received' => 'boolean',
        'refunded' => 'boolean',
        'processed' => 'boolean',
        'notification_sent' => 'boolean',
        'payment_created_at' => 'datetime',
        'payment_expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'webhook_received_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Relación con contrato
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'idcontrato', 'idcontrato');
    }

    /**
     * Marcar pago como exitoso
     */
    public function marcarComoPagado($clipResponse)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'clip_payment_response' => $clipResponse,
            'processed' => true,
        ]);
    }

    /**
     * Verificar si el pago está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar si el pago fue exitoso
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}

