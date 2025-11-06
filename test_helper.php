<?php

require __DIR__ . '/vendor/autoload.php';

use App\Helpers\ContratoHelper;

echo "=== Pruebas de ContratoHelper ===\n\n";

// Prueba 1: Género y artículo - Hombre
$curpHombre = 'XEXX010101HDFXXX00';
$resultado = ContratoHelper::obtenerGeneroYArticulo($curpHombre, 'ARRENDADOR', 'ARRENDADORA');
echo "1. CURP Hombre: {$curpHombre}\n";
echo "   Resultado: {$resultado}\n";
echo "   Esperado: EL ARRENDADOR\n\n";

// Prueba 2: Género y artículo - Mujer
$curpMujer = 'XEXX010101MDFXXX00';
$resultado = ContratoHelper::obtenerGeneroYArticulo($curpMujer, 'ARRENDATARIO', 'ARRENDATARIA');
echo "2. CURP Mujer: {$curpMujer}\n";
echo "   Resultado: {$resultado}\n";
echo "   Esperado: LA ARRENDATARIA\n\n";

// Prueba 3: Inmueble con artículo - Femenino
$resultado = ContratoHelper::obtenerInmuebleConArticulo('casa');
echo "3. Inmueble: casa\n";
echo "   Resultado: {$resultado}\n";
echo "   Esperado: una casa ubicada\n\n";

// Prueba 4: Inmueble con artículo - Masculino
$resultado = ContratoHelper::obtenerInmuebleConArticulo('departamento');
echo "4. Inmueble: departamento\n";
echo "   Resultado: {$resultado}\n";
echo "   Esperado: un departamento ubicado\n\n";

// Prueba 5: Número a letras - Simple
$resultado = ContratoHelper::numeroALetras(500);
echo "5. Número: 500\n";
echo "   Resultado: {$resultado}\n";
echo "   Esperado: QUINIENTOS\n\n";

// Prueba 6: Número a letras - Complejo
$resultado = ContratoHelper::numeroALetras(3275);
echo "6. Número: 3275\n";
echo "   Resultado: {$resultado}\n";
echo "   Esperado: TRES MIL DOSCIENTOS SETENTA Y CINCO\n\n";

// Prueba 7: Número a letras - Con decimales
$resultado = ContratoHelper::numeroALetras(1850.50);
echo "7. Número: 1850.50\n";
echo "   Resultado: {$resultado}\n";
echo "   Esperado: MIL OCHOCIENTOS CINCUENTA\n\n";

// Prueba 8: Código de estado - Ciudad de México
$resultado = ContratoHelper::obtenerCodigoEstado('Ciudad de México');
echo "8. Estado: Ciudad de México\n";
echo "   Resultado: {$resultado}\n";
echo "   Esperado: la Ciudad de México\n\n";

// Prueba 9: Código de estado - Jalisco
$resultado = ContratoHelper::obtenerCodigoEstado('Jalisco');
echo "9. Estado: Jalisco\n";
echo "   Resultado: {$resultado}\n";
echo "   Esperado: el Estado de Jalisco\n\n";

// Prueba 10: Cuenta formateada - Transferencia
$resultado = ContratoHelper::obtenerCuentaFormateada('Transferencia electrónica', '012345678901234567');
echo "10. Forma pago: Transferencia electrónica\n";
echo "    Cuenta: 012345678901234567\n";
echo "    Resultado: {$resultado}\n";
echo "    Esperado: la Cuenta CLABE 012345678901234567\n\n";

// Prueba 11: Cuenta formateada - Efectivo
$resultado = ContratoHelper::obtenerCuentaFormateada('Efectivo', 'Av. Principal 123');
echo "11. Forma pago: Efectivo\n";
echo "    Domicilio: Av. Principal 123\n";
echo "    Resultado: {$resultado}\n";
echo "    Esperado: Av. Principal 123\n\n";

echo "=== Fin de las pruebas ===\n";
echo "\nNOTA: La función generateRandomToken() no se incluyó en ContratoHelper\n";
echo "porque el sistema ya tiene su propio método en ContratoController::generarTokenDesdeDatos()\n";

