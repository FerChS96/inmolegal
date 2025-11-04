<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contrato extends Model
{
    use HasFactory;

    protected $primaryKey = 'idcontrato';

    protected $fillable = [
        'token',
        'email',
        
        // Arrendador
        'nombres_arrendador',
        'apellido_paterno_arrendador',
        'apellido_materno_arrendador',
        'curp_arrendador',
        
        // Arrendatario
        'nombres_arrendatario',
        'apellido_paterno_arrendatario',
        'apellido_materno_arrendatario',
        'curp_arrendatario',
        
        // Fiador
        'tiene_fiador',
        'nombres_fiador',
        'apellido_paterno_fiador',
        'apellido_materno_fiador',
        'curp_fiador',
        
        // Inmueble
        'tipo_inmueble',
        'uso_inmueble',
        
        // Dirección
        'calle',
        'numero_exterior',
        'numero_interior',
        'colonia',
        'codigo_postal',
        'ciudad',
        'codigo_estado',
        
        // Contrato
        'fecha_inicio',
        'plazo_meses',
        'precio_mensual',
        'forma_pago',
        'cuenta_domicilio',
        
        // Estado del pago
        'pagado',
        'monto_pagado',
        'fecha_pago',
        'metodo_pago',
        'pago_id',
    ];

    protected $casts = [
        'tiene_fiador' => 'boolean',
        'pagado' => 'boolean',
        'fecha_inicio' => 'date',
        'precio_mensual' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    /**
     * Generar un token único para el contrato
     */
    public static function generateToken(): string
    {
        do {
            $token = strtoupper(Str::random(8));
        } while (self::where('token', $token)->exists());
        
        return $token;
    }

    /**
     * Relación con pagos
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'idcontrato', 'idcontrato');
    }

    /**
     * Obtener el pago activo/más reciente
     */
    public function pagoActual()
    {
        return $this->hasOne(Pago::class, 'idcontrato', 'idcontrato')->latest();
    }

    /**
     * Incrementar el contador de descargas
     */
    public function incrementarDescargas()
    {
        $this->increment('descargas');
        $this->update(['ultima_descarga' => now()]);
    }

    /**
     * Marcar como pagado
     */
    public function marcarComoPagado($monto, $metodoPago, $pagoId)
    {
        $this->update([
            'pagado' => true,
            'monto_pagado' => $monto,
            'fecha_pago' => now(),
            'metodo_pago' => $metodoPago,
            'pago_id' => $pagoId,
        ]);
    }
}

