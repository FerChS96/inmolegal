<?php

namespace App\Helpers;

class ContratoHelper
{
    /**
     * Determina el género y artículo basado en el CURP
     * 
     * @param string $curp
     * @param string $masculino
     * @param string $femenino
     * @return string "EL ARRENDADOR" o "LA ARRENDADORA"
     */
    public static function obtenerGeneroYArticulo($curp, $masculino, $femenino)
    {
        if (strlen($curp) < 11) {
            return '';
        }
        
        $genero = strtoupper($curp[10]); // El 11º carácter determina el género
        
        if ($genero === 'H') {
            return "EL {$masculino}";
        } elseif ($genero === 'M') {
            return "LA {$femenino}";
        }
        
        return ''; // CURP inválida
    }

    /**
     * Determina solo el artículo (el o la) basado en el CURP
     * 
     * @param string $curp
     * @return string "EL" o "LA"
     */
    public static function determinarGenero($curp)
    {
        if (strlen($curp) < 11) {
            return '';
        }
        
        $genero = strtoupper($curp[10]);
        
        if ($genero === 'H') {
            return 'EL';
        } elseif ($genero === 'M') {
            return 'LA';
        }
        
        return '';
    }

    /**
     * Obtiene el inmueble con el artículo correcto
     * 
     * @param string $inmueble
     * @return string "una casa ubicada" o "un departamento ubicado"
     */
    public static function obtenerInmuebleConArticulo($inmueble)
    {
        $inmuebleLower = strtolower($inmueble);
        $femeninos = ['casa', 'oficina', 'bodega'];
        
        if (in_array($inmuebleLower, $femeninos)) {
            return "una {$inmuebleLower} ubicada";
        } else {
            return "un {$inmuebleLower} ubicado";
        }
    }

    /**
     * Convierte un número a letras
     * 
     * @param float|int $numero
     * @return string
     */
    public static function numeroALetras($numero)
    {
        $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
        $especiales = ['', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];

        $num = intval($numero);
        if ($num === 0) return 'CERO';
        
        $letras = '';

        // Miles
        if ($num >= 1000) {
            $miles = intval($num / 1000);
            if ($miles === 1) {
                $letras .= 'MIL ';
            } else {
                $letras .= self::convertirGrupo($miles, $unidades, $decenas, $centenas, $especiales) . ' MIL ';
            }
            $num %= 1000;
        }

        // Centenas, decenas y unidades
        $letras .= self::convertirGrupo($num, $unidades, $decenas, $centenas, $especiales);

        return trim($letras);
    }

    /**
     * Convierte un grupo de hasta 3 dígitos a letras
     * 
     * @param int $num
     * @param array $unidades
     * @param array $decenas
     * @param array $centenas
     * @param array $especiales
     * @return string
     */
    private static function convertirGrupo($num, $unidades, $decenas, $centenas, $especiales)
    {
        $letras = '';

        // Centenas
        if ($num >= 100) {
            if ($num === 100) {
                $letras .= 'CIEN ';
            } else {
                $letras .= $centenas[intval($num / 100)] . ' ';
            }
            $num %= 100;
        }

        // Decenas especiales (10-19)
        if ($num >= 10 && $num <= 19) {
            if ($num === 10) {
                $letras .= 'DIEZ ';
            } else {
                $letras .= $especiales[$num - 10] . ' ';
            }
            return $letras;
        }

        // Decenas (20-29 con unión)
        if ($num >= 20 && $num < 30) {
            if ($num === 20) {
                $letras .= 'VEINTE ';
            } else {
                $letras .= 'VEINTI' . $unidades[$num % 10] . ' ';
            }
            return $letras;
        }

        // Decenas (30-99)
        if ($num >= 30) {
            $letras .= $decenas[intval($num / 10)] . ' ';
            $num %= 10;
            if ($num > 0) {
                $letras .= 'Y ' . $unidades[$num] . ' ';
            }
        } else if ($num > 0) {
            $letras .= $unidades[$num] . ' ';
        }

        return $letras;
    }

    /**
     * Obtiene el código del estado formateado
     * 
     * @param string $estado
     * @return string "la Ciudad de México" o "el Estado de Jalisco"
     */
    public static function obtenerCodigoEstado($estado)
    {
        if ($estado === 'Ciudad de México' || $estado === 'CDMX') {
            return "la Ciudad de México";
        } else {
            return "el Estado de {$estado}";
        }
    }

    /**
     * Obtiene el formato de cuenta según la forma de pago
     * 
     * @param string $formaPago
     * @param string $cuenta
     * @return string
     */
    public static function obtenerCuentaFormateada($formaPago, $cuenta)
    {
        $formaPagoUpper = strtoupper($formaPago);
        
        if ($formaPagoUpper === 'TRANSFERENCIA' || 
            strtolower($formaPago) === 'transferencia electrónica' || 
            strtolower($formaPago) === 'transferencia electronica') {
            return "la Cuenta CLABE {$cuenta}";
        }
        
        return $cuenta;
    }
}
