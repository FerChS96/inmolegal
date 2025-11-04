<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            
            // Información del Payment Link (Checkout Redireccionado de Clip)
            $table->string('payment_request_id')->unique()->nullable();
            $table->text('checkout_url')->nullable();
            
            // Información de la Transacción
            $table->string('clip_payment_id')->unique()->nullable();
            $table->string('clip_order_id')->nullable();
            
            // Datos del Monto
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MXN');
            $table->decimal('fee_amount', 10, 2)->nullable();
            $table->decimal('net_amount', 10, 2)->nullable();
            
            // Estado del Pago
            $table->string('status', 50)->default('pending'); // pending, paid, cancelled, expired, refunded
            
            // Método de Pago
            $table->string('payment_method', 50)->nullable();
            $table->string('card_brand', 50)->nullable();
            $table->string('card_last4', 4)->nullable();
            $table->string('card_type', 20)->nullable();
            $table->string('card_issuer', 100)->nullable();
            
            // Fechas
            $table->timestamp('payment_created_at')->nullable();
            $table->timestamp('payment_expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Respuesta de Clip (JSON)
            $table->json('clip_checkout_response')->nullable();
            $table->json('clip_payment_response')->nullable();
            $table->json('clip_transaction_details')->nullable();
            
            // Webhook
            $table->boolean('webhook_received')->default(false);
            $table->json('webhook_data')->nullable();
            $table->timestamp('webhook_received_at')->nullable();
            $table->integer('webhook_attempts')->default(0);
            
            // Cliente
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 20)->nullable();
            
            // Reembolsos
            $table->boolean('refunded')->default(false);
            $table->string('refund_id')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            // Metadata
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            
            // Errores
            $table->string('error_code', 50)->nullable();
            $table->text('error_message')->nullable();
            $table->integer('attempts')->default(1);
            
            // Auditoría
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Control Interno
            $table->boolean('processed')->default(false);
            $table->boolean('notification_sent')->default(false);
            
            $table->timestamps();
            
            // Índices
            $table->index('contrato_id');
            $table->index('payment_request_id');
            $table->index('clip_payment_id');
            $table->index('status');
            $table->index('paid_at');
            $table->index(['status', 'paid_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
