<?php
/**
 * TodoCamisetas API
 * Punto de entrada único de la API RESTful
 * 
 * @version 1.0.0
 * @author API Development Team
 */

// Incluir configuración
require_once __DIR__ . '/config/config.php';

// Manejo global de errores
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function($exception) {
    error_log("Error no capturado: " . $exception->getMessage());
    Response::serverError('Error interno del servidor');
});

try {
    // Inicializar y manejar rutas
    $router = new ApiRoutes();
    $router->handle();
    
} catch (Exception $e) {
    error_log("Error en index.php: " . $e->getMessage());
    Response::serverError('Error interno del servidor');
} 