<?php
/**
 * Configuración General de la API
 * TodoCamisetas API
 */

// Configuración general
define('API_VERSION', '1.0.0');
define('API_NAME', 'TodoCamisetas API');
define('API_DESCRIPTION', 'API RESTful para gestión de camisetas deportivas');

// Configuración de Rate Limiting
define('RATE_LIMIT_MAX_REQUESTS', 100);
define('RATE_LIMIT_WINDOW_SECONDS', 60);
define('RATE_LIMIT_RETRY_AFTER', 10);

// Configuración de CORS
define('CORS_ALLOWED_ORIGINS', '*');
define('CORS_ALLOWED_METHODS', 'GET, POST, PUT, DELETE, OPTIONS');
define('CORS_ALLOWED_HEADERS', 'Content-Type, Authorization');

// Configuración de zona horaria
date_default_timezone_set('America/Santiago');

// Configuración de errores (desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoload de clases
spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/../models/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../utils/',
        __DIR__ . '/../config/',
        __DIR__ . '/../routes/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});

// Incluir configuración de base de datos
require_once __DIR__ . '/database.php';

// Incluir sistema de rutas
require_once __DIR__ . '/../routes/api.php'; 