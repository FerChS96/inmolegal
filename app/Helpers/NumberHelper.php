<?php

if (!function_exists('numero_a_letras')) {
    /**
     * Convertir número a letras en español
     * 
     * @param float $numero
     * @return string
     */
    function numero_a_letras($numero)
    {
        $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $especiales = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
        
        $numero = (int) $numero;
        
        if ($numero == 0) return 'CERO';
        if ($numero == 100) return 'CIEN';
        
        $resultado = '';
        
        // Millones
        if ($numero >= 1000000) {
            $millones = (int) ($numero / 1000000);
            if ($millones == 1) {
                $resultado .= 'UN MILLÓN ';
            } else {
                $resultado .= convertir_centenas($millones) . ' MILLONES ';
            }
            $numero %= 1000000;
        }
        
        // Miles
        if ($numero >= 1000) {
            $miles = (int) ($numero / 1000);
            if ($miles == 1) {
                $resultado .= 'MIL ';
            } else {
                $resultado .= convertir_centenas($miles) . ' MIL ';
            }
            $numero %= 1000;
        }
        
        // Centenas, decenas y unidades
        if ($numero > 0) {
            $resultado .= convertir_centenas($numero);
        }
        
        return trim($resultado);
    }
}

if (!function_exists('convertir_centenas')) {
    /**
     * Convertir centenas, decenas y unidades
     * 
     * @param int $numero
     * @return string
     */
    function convertir_centenas($numero)
    {
        $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $especiales = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
        
        $resultado = '';
        
        // Centenas
        if ($numero >= 100) {
            $c = (int) ($numero / 100);
            if ($numero == 100) {
                return 'CIEN';
            }
            $resultado .= $centenas[$c] . ' ';
            $numero %= 100;
        }
        
        // Decenas y unidades
        if ($numero >= 10 && $numero < 20) {
            $resultado .= $especiales[$numero - 10];
        } elseif ($numero >= 20) {
            $d = (int) ($numero / 10);
            $u = $numero % 10;
            $resultado .= $decenas[$d];
            if ($u > 0) {
                $resultado .= ' Y ' . $unidades[$u];
            }
        } elseif ($numero > 0) {
            $resultado .= $unidades[$numero];
        }
        
        return trim($resultado);
    }
}
