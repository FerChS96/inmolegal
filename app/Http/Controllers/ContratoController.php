<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Pago;
use App\Helpers\ContratoHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ContratoController extends Controller
{
    /**
     * Mostrar el formulario de generación de contratos
     */
    public function mostrarFormulario()
    {
        return view('contratos.formulario');
    }

    /**
     * Procesar el formulario y preparar el pago
     */
    public function procesarFormulario(Request $request)
    {
        // Validar datos del formulario
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            // Arrendador
            'nombres_arrendador' => 'required|string|max:100',
            'apellido_paterno_arrendador' => 'required|string|max:100',
            'apellido_materno_arrendador' => 'required|string|max:100',
            'curp_arrendador' => ['required', 'string', 'size:18', 'regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/'],
            // Arrendatario
            'nombres_arrendatario' => 'required|string|max:100',
            'apellido_paterno_arrendatario' => 'required|string|max:100',
            'apellido_materno_arrendatario' => 'required|string|max:100',
            'curp_arrendatario' => ['required', 'string', 'size:18', 'regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/'],
            // Fiador (opcional)
            'tiene_fiador' => 'required|boolean',
            'nombres_fiador' => 'nullable|required_if:tiene_fiador,true|string|max:100',
            'apellido_paterno_fiador' => 'nullable|required_if:tiene_fiador,true|string|max:100',
            'apellido_materno_fiador' => 'nullable|required_if:tiene_fiador,true|string|max:100',
            'curp_fiador' => ['nullable', 'required_if:tiene_fiador,true', 'string', 'size:18', 'regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/'],
            // Inmueble
            'tipo_inmueble' => 'required|string',
            'uso_inmueble' => 'required|string',
            // Dirección
            'calle' => 'required|string|max:255',
            'numero_exterior' => 'required|string|max:50',
            'numero_interior' => 'nullable|string|max:50',
            'colonia' => 'required|string|max:255',
            'codigo_postal' => 'required|string|size:5|regex:/^[0-9]{5}$/',
            'ciudad' => 'required|string|max:255',
            'estado' => 'required|string|max:10',
            // Contrato
            'fecha_inicio' => 'required|date',
            'plazo_meses' => 'required|integer|min:1|max:48',
            'pago' => 'required|numeric|min:1',
            'forma_pago' => 'required|string|in:EFECTIVO,TRANSFERENCIA',
            'cuenta_domicilio' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $formaPago = $request->input('forma_pago');
                    
                    if ($formaPago === 'TRANSFERENCIA') {
                        // Si es transferencia, debe ser CLABE de 18 dígitos
                        if (!preg_match('/^[0-9]{18}$/', $value)) {
                            $fail('La Cuenta CLABE debe tener exactamente 18 dígitos numéricos.');
                        }
                    } elseif ($formaPago === 'EFECTIVO') {
                        // Si es efectivo, debe ser un domicilio con al menos 10 caracteres
                        if (strlen($value) < 10) {
                            $fail('El domicilio para pago debe tener al menos 10 caracteres.');
                        }
                        if (strlen($value) > 255) {
                            $fail('El domicilio no puede exceder 255 caracteres.');
                        }
                    }
                }
            ],
        ], [
            'curp_arrendador.size' => 'El CURP del arrendador debe tener exactamente 18 caracteres',
            'curp_arrendador.regex' => 'El CURP del arrendador no tiene un formato válido',
            'curp_arrendatario.size' => 'El CURP del arrendatario debe tener exactamente 18 caracteres',
            'curp_arrendatario.regex' => 'El CURP del arrendatario no tiene un formato válido',
            'curp_fiador.size' => 'El CURP del fiador debe tener exactamente 18 caracteres',
            'curp_fiador.regex' => 'El CURP del fiador no tiene un formato válido',
            'codigo_postal.size' => 'El código postal debe tener exactamente 5 dígitos',
            'codigo_postal.regex' => 'El código postal solo debe contener números',
            'plazo_meses.max' => 'El plazo máximo es de 48 meses',
            'pago.min' => 'El pago debe ser mayor a cero',
            'forma_pago.in' => 'La forma de pago debe ser EFECTIVO o TRANSFERENCIA',
            'cuenta_domicilio.required' => 'Debe proporcionar la cuenta CLABE o el domicilio según la forma de pago',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generar token único basado en datos del arrendatario
            // Formato: 3 letras apellido paterno + 2 letras apellido materno + inicial nombre + año/mes (4 dígitos) + 4 dígitos aleatorios
            $token = $this->generarTokenDesdeDatos(
                $request->nombres_arrendatario,
                $request->apellido_paterno_arrendatario,
                $request->apellido_materno_arrendatario
            );

            // Asegurar unicidad del token
            while (Contrato::where('token', $token)->exists()) {
                $token = $this->generarTokenDesdeDatos(
                    $request->nombres_arrendatario,
                    $request->apellido_paterno_arrendatario,
                    $request->apellido_materno_arrendatario
                );
            }

            // PRECIO FIJO: $19 MXN para todos los contratos
            $monto = 19.00;

            // Calcular campos adicionales usando ContratoHelper
            $elArrendador = ContratoHelper::obtenerGeneroYArticulo($request->curp_arrendador, 'ARRENDADOR', 'ARRENDADORA');
            $elArrendatario = ContratoHelper::obtenerGeneroYArticulo($request->curp_arrendatario, 'ARRENDATARIO', 'ARRENDATARIA');
            $inmuebleObjeto = ContratoHelper::obtenerInmuebleConArticulo($request->tipo_inmueble);
            $precioEnLetra = ContratoHelper::numeroALetras($monto);
            $codigoEstado = ContratoHelper::obtenerCodigoEstado($request->estado);
            $cuentaFormateada = ContratoHelper::obtenerCuentaFormateada($request->forma_pago, $request->cuenta_domicilio ?? '');
            
            // Calcular campos del fiador si existe
            $elFiador = null;
            $elFiador1 = null;
            $nombreFiador1 = null;
            $clausulaFiador = null;
            
            if ($request->boolean('tiene_fiador') && $request->curp_fiador) {
                $elFiador = ContratoHelper::obtenerGeneroYArticulo($request->curp_fiador, 'FIADOR', 'FIADORA');
                $elFiador1 = "y como {$elFiador} ";
                $nombreFiador = strtoupper($request->nombres_fiador . ' ' . $request->apellido_paterno_fiador . ' ' . $request->apellido_materno_fiador);
                $nombreFiador1 = "{$nombreFiador} con CURP ";
                $clausulaFiador = "<b>Décima primera Bis.</b> {$elFiador}, se constituye como personal y solidario de las obligaciones dimanantes de este contrato de arrendamiento. Manifiesta que su obligación será vinculante tanto durante el periodo contractual como durante las sucesivas prórrogas que pudieran producirse, del tipo que sean, e incluso para el caso de tácita reconducción. {$elFiador} se obliga a pagar o cumplir todas y cada una de las obligaciones de la parte arrendataria en caso de incumplimiento y renuncia en este acto al beneficio de orden y al derecho de excusión.";
            }

            // Crear registro de contrato (temporal, sin pagar)
            $contrato = Contrato::create([
                'token' => $token,
                'email' => $request->email,
                // Arrendador
                'nombres_arrendador' => strtoupper($request->nombres_arrendador),
                'apellido_paterno_arrendador' => strtoupper($request->apellido_paterno_arrendador),
                'apellido_materno_arrendador' => strtoupper($request->apellido_materno_arrendador),
                'curp_arrendador' => strtoupper($request->curp_arrendador),
                // Arrendatario
                'nombres_arrendatario' => strtoupper($request->nombres_arrendatario),
                'apellido_paterno_arrendatario' => strtoupper($request->apellido_paterno_arrendatario),
                'apellido_materno_arrendatario' => strtoupper($request->apellido_materno_arrendatario),
                'curp_arrendatario' => strtoupper($request->curp_arrendatario),
                // Fiador
                'tiene_fiador' => $request->boolean('tiene_fiador'),
                'nombres_fiador' => $request->nombres_fiador ? strtoupper($request->nombres_fiador) : null,
                'apellido_paterno_fiador' => $request->apellido_paterno_fiador ? strtoupper($request->apellido_paterno_fiador) : null,
                'apellido_materno_fiador' => $request->apellido_materno_fiador ? strtoupper($request->apellido_materno_fiador) : null,
                'curp_fiador' => $request->curp_fiador ? strtoupper($request->curp_fiador) : null,
                // Inmueble
                'tipo_inmueble' => $request->tipo_inmueble,
                'uso_inmueble' => $request->uso_inmueble,
                // Dirección
                'calle' => $request->calle,
                'numero_exterior' => $request->numero_exterior,
                'numero_interior' => $request->numero_interior,
                'colonia' => $request->colonia,
                'codigo_postal' => $request->codigo_postal,
                'ciudad' => strtoupper($request->ciudad),
                'codigo_estado' => $request->estado,
                // Contrato
                'fecha_inicio' => $request->fecha_inicio,
                'plazo_meses' => $request->plazo_meses,
                'precio_mensual' => $monto,
                'forma_pago' => $request->forma_pago,
                'cuenta_domicilio' => $request->cuenta_domicilio ?? null,
                'pagado' => false,
                // Campos calculados
                'el_arrendador' => $elArrendador,
                'el_arrendatario' => $elArrendatario,
                'inmueble_objeto' => $inmuebleObjeto,
                'precio_en_letra' => $precioEnLetra,
                'codigo_estado_texto' => $codigoEstado,
                'cuenta_formateada' => $cuentaFormateada,
                'el_fiador' => $elFiador,
                'el_fiador1' => $elFiador1,
                'nombre_fiador1' => $nombreFiador1,
                'clausula_fiador' => $clausulaFiador,
            ]);

            // Crear registro de pago pendiente
            $pago = Pago::create([
                'idcontrato' => $contrato->idcontrato,
                'amount' => $monto,
                'currency' => 'MXN',
                'status' => 'pending',
                'description' => 'Generación de Contrato de Arrendamiento - ' . $request->forma_pago,
                'customer_email' => $request->email,
                'payment_method' => $request->forma_pago, // TRANSFERENCIA o EFECTIVO
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Retornar datos para iniciar Clip
            return response()->json([
                'success' => true,
                'contrato_id' => $contrato->idcontrato,
                'pago_id' => $pago->idpago,
                'token' => $token,
                'amount' => $monto,
                'redirect_payment' => route('clip.iniciar-pago', ['pago' => $pago->idpago])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el formulario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar PDF del contrato usando el token
     */
    public function descargar($token)
    {
        $contrato = Contrato::where('token', $token)->firstOrFail();

        if (!$contrato->pagado) {
            abort(403, 'Este contrato no ha sido pagado aún');
        }

        if (!$contrato->pdf_path || !file_exists(storage_path('app/' . $contrato->pdf_path))) {
            abort(404, 'PDF no encontrado');
        }

        // Incrementar contador de descargas
        $contrato->incrementarDescargas();

        return response()->download(
            storage_path('app/' . $contrato->pdf_path),
            "contrato_{$token}.pdf"
        );
    }

    /**
     * Convertir número a letra (versión simplificada)
     * En producción, usar una librería como NumberToWords
     */
    private function convertirNumeroALetra($numero)
    {
        // Esta es una implementación básica
        // Para producción, instalar: composer require kwn/number-to-words
        $numero = number_format($numero, 2, '.', '');
        return strtoupper($numero); // Por ahora retorna el número
        
        // Con la librería sería:
        // $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        // return $formatter->format($numero);
    }

    /**
     * Generar token único según especificación:
     * - 3 primeras letras del apellido paterno
     * - 2 últimas letras del apellido materno
     * - 1 inicial del nombre
     * - Año y mes actual en 4 dígitos (YYMM) - por no tener fecha de nacimiento
     * - 4 dígitos aleatorios
     * 
     * Ejemplo: GARCIAM2511XXXX (García Martínez, año 2025, mes 11, random XXXX)
     */
    private function generarTokenDesdeDatos(string $nombres, string $apellidoPaterno, string $apellidoMaterno): string
    {
        // Limpiar y normalizar textos
        $nombres = $this->limpiarTexto($nombres);
        $apellidoPaterno = $this->limpiarTexto($apellidoPaterno);
        $apellidoMaterno = $this->limpiarTexto($apellidoMaterno);

        // 3 primeras letras del apellido paterno
        $segmentoPaterno = strtoupper(substr($apellidoPaterno, 0, 3));
        $segmentoPaterno = str_pad($segmentoPaterno, 3, 'X');

        // 2 últimas letras del apellido materno
        $segmentoMaterno = strtoupper(substr($apellidoMaterno, -2));
        $segmentoMaterno = str_pad($segmentoMaterno, 2, 'X', STR_PAD_LEFT);

        // Primera letra del nombre
        $inicialNombre = strtoupper(substr($nombres, 0, 1));
        $inicialNombre = $inicialNombre ?: 'X';

        // Año y mes actual en formato YYMM (como no tenemos fecha de nacimiento)
        $yearMonth = date('ym'); // Ej: 2511 para noviembre 2025

        // 4 dígitos aleatorios
        $codigoAleatorio = str_pad((string) rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return $segmentoPaterno . $segmentoMaterno . $inicialNombre . $yearMonth . $codigoAleatorio;
    }

    private function limpiarTexto(string $texto): string
    {
        $ascii = Str::upper(Str::ascii($texto));
        return preg_replace('/[^A-Z\s]/', '', $ascii) ?: 'X';
    }
}

