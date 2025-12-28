<?php
/**
 * Fix SIMPLIFICADO para el conflicto entre extensión psr (PHP 8.4) y Monolog
 * 
 * PROBLEMA: La extensión psr define PsrExt\Log\LoggerInterface pero Monolog
 *           ya está modificado para usar \Psr\Log\LoggerInterface.
 * 
 * SOLUCIÓN: NO hacer nada agresivo. El archivo de Monolog ya está corregido
 *           para usar el namespace completo. Solo verificamos que esto funcione.
 */

if (!extension_loaded('psr')) {
    return; // No hay conflicto si la extensión no está cargada
}

// NO cargar las clases manualmente - esto causa conflictos
// NO crear aliases - esto causa redeclaraciones
// El archivo de Monolog ya tiene el namespace completo correcto
// Solo dejamos que funcione normalmente
