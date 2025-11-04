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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            
            // Token único para descarga
            $table->string('token', 50)->unique();
            
            // Información de contacto
            $table->string('email');
            
            // Datos del Arrendador
            $table->string('nombre_arrendador');
            $table->string('curp_arrendador', 18);
            
            // Datos del Arrendatario
            $table->string('nombre_arrendatario');
            $table->string('curp_arrendatario', 18);
            
            // Datos del Fiador (opcional)
            $table->boolean('tiene_fiador')->default(false);
            $table->string('nombre_fiador')->nullable();
            $table->string('curp_fiador', 18)->nullable();
            
            // Datos del Inmueble
            $table->string('tipo_inmueble', 50);
            $table->string('uso_inmueble');
            $table->text('ubicacion');
            $table->string('estado', 100);
            
            // Detalles del Contrato
            $table->integer('dia_inicio');
            $table->string('mes_inicio', 20);
            $table->integer('anio_inicio');
            $table->integer('plazo_meses');
            
            $table->decimal('precio_mensual', 10, 2);
            $table->string('precio_mensual_letra');
            
            $table->string('forma_pago', 50);
            $table->text('cuenta_domicilio');
            
            // Estado y Pago
            $table->boolean('pagado')->default(false);
            $table->decimal('monto_pagado', 10, 2)->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->string('metodo_pago', 50)->nullable();
            $table->string('pago_id')->nullable();
            
            // Auditoría
            $table->string('ip_cliente', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('descargas')->default(0);
            $table->timestamp('ultima_descarga')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('token');
            $table->index('email');
            $table->index('pagado');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
