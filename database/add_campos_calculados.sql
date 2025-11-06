-- ============================================
-- SCRIPT: Agregar Campos Calculados a Tabla Contratos
-- Fecha: 6 de noviembre de 2025
-- Descripción: Agrega columnas para campos calculados automáticamente
--              usando las funciones del ContratoHelper
-- ============================================

-- IMPORTANTE: Ejecutar este script en la base de datos PostgreSQL
-- después de haber implementado el ContratoHelper en Laravel

BEGIN;

-- ============================================
-- Agregar columnas para campos calculados
-- ============================================

-- Campos principales de género y formato
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS el_arrendador VARCHAR(50) NULL;
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS el_arrendatario VARCHAR(50) NULL;
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS inmueble_objeto VARCHAR(100) NULL;
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS precio_en_letra VARCHAR(500) NULL;
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS codigo_estado_texto VARCHAR(100) NULL;
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS cuenta_formateada VARCHAR(255) NULL;

-- Campos del fiador (si aplica)
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS el_fiador VARCHAR(50) NULL;
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS el_fiador1 VARCHAR(100) NULL;
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS nombre_fiador1 VARCHAR(255) NULL;
ALTER TABLE contratos ADD COLUMN IF NOT EXISTS clausula_fiador TEXT NULL;

-- ============================================
-- Agregar comentarios para documentación
-- ============================================

COMMENT ON COLUMN contratos.el_arrendador IS '"EL ARRENDADOR" / "LA ARRENDADORA" - Calculado según CURP (carácter 11: H/M)';
COMMENT ON COLUMN contratos.el_arrendatario IS '"EL ARRENDATARIO" / "LA ARRENDATARIA" - Calculado según CURP (carácter 11: H/M)';
COMMENT ON COLUMN contratos.inmueble_objeto IS '"una casa ubicada" / "un departamento ubicado" - Artículo según género del inmueble';
COMMENT ON COLUMN contratos.precio_en_letra IS 'Precio convertido a letras en mayúsculas (ej: "TRES MIL DOSCIENTOS PESOS")';
COMMENT ON COLUMN contratos.codigo_estado_texto IS '"el Estado de Jalisco" / "la Ciudad de México" - Formato para documentos legales';
COMMENT ON COLUMN contratos.cuenta_formateada IS '"la Cuenta CLABE 012345..." o domicilio formateado según forma de pago';
COMMENT ON COLUMN contratos.el_fiador IS '"EL FIADOR" / "LA FIADORA" - Calculado según CURP del fiador (si existe)';
COMMENT ON COLUMN contratos.el_fiador1 IS '"y como EL FIADOR " - Texto conectivo para el contrato (si existe fiador)';
COMMENT ON COLUMN contratos.nombre_fiador1 IS '"JUAN PÉREZ GARCÍA con CURP " - Nombre completo del fiador con texto (si existe)';
COMMENT ON COLUMN contratos.clausula_fiador IS 'Texto completo HTML de la cláusula del fiador (Décima primera Bis)';

-- ============================================
-- Crear índices para mejorar rendimiento
-- ============================================

-- Índice para búsquedas por género del arrendador
CREATE INDEX IF NOT EXISTS idx_contratos_el_arrendador ON contratos(el_arrendador);

-- Índice para búsquedas por género del arrendatario
CREATE INDEX IF NOT EXISTS idx_contratos_el_arrendatario ON contratos(el_arrendatario);

-- Índice para búsquedas de contratos con fiador
CREATE INDEX IF NOT EXISTS idx_contratos_con_fiador ON contratos(tiene_fiador, el_fiador) WHERE tiene_fiador = TRUE;

COMMIT;

-- ============================================
-- Verificación de columnas agregadas
-- ============================================

-- Ejecutar esta consulta para verificar que las columnas se agregaron correctamente:
-- SELECT column_name, data_type, character_maximum_length, is_nullable 
-- FROM information_schema.columns 
-- WHERE table_name = 'contratos' 
-- AND column_name IN ('el_arrendador', 'el_arrendatario', 'inmueble_objeto', 
--                     'precio_en_letra', 'codigo_estado_texto', 'cuenta_formateada',
--                     'el_fiador', 'el_fiador1', 'nombre_fiador1', 'clausula_fiador')
-- ORDER BY column_name;

-- ============================================
-- NOTAS IMPORTANTES
-- ============================================

-- 1. Estos campos se calculan automáticamente en app/Http/Controllers/ContratoController.php
--    usando las funciones del app/Helpers/ContratoHelper.php

-- 2. Los campos son nullable (NULL) porque:
--    - Los contratos existentes no tienen estos valores
--    - Los campos del fiador solo aplican si tiene_fiador = TRUE

-- 3. Para actualizar contratos existentes con estos valores calculados,
--    se necesitaría crear un script de migración de datos en Laravel
--    que recalcule todos los campos basándose en los datos actuales

-- 4. Los nuevos contratos automáticamente tendrán estos campos poblados

-- 5. Funciones disponibles en ContratoHelper:
--    - obtenerGeneroYArticulo($curp, $masculino, $femenino)
--    - determinarGenero($curp)
--    - obtenerInmuebleConArticulo($inmueble)
--    - numeroALetras($numero)
--    - obtenerCodigoEstado($estado)
--    - obtenerCuentaFormateada($formaPago, $cuenta)

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
