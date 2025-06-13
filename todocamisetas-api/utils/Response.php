<?php
/**
 * Clase Response
 * Maneja todas las respuestas HTTP de la API con cabeceras estándar
 */

class Response {
    
    /**
     * Envía una respuesta JSON con las cabeceras estándar
     * 
     * @param array $data Datos a enviar
     * @param int $statusCode Código de estado HTTP
     * @param array $headers Cabeceras adicionales
     */
    public static function json($data, $statusCode = 200, $headers = []) {
        // Cabeceras HTTP obligatorias
        self::setStandardHeaders();
        
        // Cabeceras adicionales
        foreach ($headers as $header => $value) {
            header("$header: $value");
        }
        
        // Código de estado HTTP
        http_response_code($statusCode);
        
        // Enviar respuesta JSON
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Envía una respuesta de éxito
     * 
     * @param array $data Datos a enviar
     * @param int $statusCode Código de estado (200 por defecto)
     */
    public static function success($data, $statusCode = 200) {
        self::json($data, $statusCode);
    }
    
    /**
     * Envía una respuesta de error
     * 
     * @param string $message Mensaje de error
     * @param int $statusCode Código de estado HTTP
     * @param array $details Detalles adicionales del error
     */
    public static function error($message, $statusCode = 400, $details = []) {
        $errorData = ['error' => $message];
        
        if (!empty($details)) {
            $errorData['details'] = $details;
        }
        
        self::json($errorData, $statusCode);
    }
    
    /**
     * Respuesta para recurso no encontrado
     * 
     * @param string $resource Nombre del recurso
     */
    public static function notFound($resource = 'Recurso') {
        self::error("$resource no encontrado", 404);
    }
    
    /**
     * Respuesta para datos inválidos
     * 
     * @param string $message Mensaje de error
     * @param array $details Detalles de validación
     */
    public static function badRequest($message = 'Datos inválidos', $details = []) {
        self::error($message, 400, $details);
    }
    
    /**
     * Respuesta para conflictos de integridad
     * 
     * @param string $message Mensaje de error
     */
    public static function conflict($message) {
        self::error($message, 409);
    }
    
    /**
     * Respuesta para rate limiting
     */
    public static function tooManyRequests() {
        $headers = ['Retry-After' => RATE_LIMIT_RETRY_AFTER];
        self::json(['error' => 'Demasiadas solicitudes'], 429, $headers);
    }
    
    /**
     * Respuesta para errores internos del servidor
     * 
     * @param string $message Mensaje de error
     */
    public static function serverError($message = 'Error interno del servidor') {
        self::error($message, 500);
    }
    
    /**
     * Respuesta para creación exitosa
     * 
     * @param array $data Datos del recurso creado
     */
    public static function created($data) {
        self::json($data, 201);
    }
    
    /**
     * Configura las cabeceras HTTP estándar obligatorias
     */
    private static function setStandardHeaders() {
        header('Content-Type: application/json; charset=utf-8');
        header('X-RateLimit-Limit: ' . RATE_LIMIT_MAX_REQUESTS);
        header('X-RateLimit-Remaining: 99'); // En una implementación real, esto sería dinámico
        header('X-RateLimit-Reset: ' . RATE_LIMIT_WINDOW_SECONDS);
        header('Access-Control-Allow-Origin: ' . CORS_ALLOWED_ORIGINS);
        header('Access-Control-Allow-Methods: ' . CORS_ALLOWED_METHODS);
        header('Access-Control-Allow-Headers: ' . CORS_ALLOWED_HEADERS);
        header('Access-Control-Max-Age: 86400'); // 24 horas
        
        // Cabeceras de seguridad
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
    }
    
    /**
     * Maneja las peticiones OPTIONS para CORS preflight
     */
    public static function handleCorsPreFlight() {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            self::setStandardHeaders();
            http_response_code(200);
            exit;
        }
    }
} 