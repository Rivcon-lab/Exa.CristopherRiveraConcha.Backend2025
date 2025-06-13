<?php
/**
 * Clase Validator
 * Valida datos de entrada según reglas específicas
 */

class Validator {
    
    private $errors = [];
    
    /**
     * Valida los datos de una camiseta
     * 
     * @param array $data Datos a validar
     * @param bool $isUpdate Si es una actualización (campos opcionales)
     * @return array Errores de validación
     */
    public function validateCamiseta($data, $isUpdate = false) {
        $this->errors = [];
        
        // Título
        if (!$isUpdate || isset($data['titulo'])) {
            $this->validateRequired('titulo', $data);
            $this->validateMaxLength('titulo', $data, 150);
        }
        
        // Club
        if (!$isUpdate || isset($data['club'])) {
            $this->validateRequired('club', $data);
            $this->validateMaxLength('club', $data, 100);
        }
        
        // País
        if (!$isUpdate || isset($data['pais'])) {
            $this->validateRequired('pais', $data);
            $this->validateMaxLength('pais', $data, 50);
        }
        
        // Tipo
        if (!$isUpdate || isset($data['tipo'])) {
            $this->validateRequired('tipo', $data);
            $this->validateEnum('tipo', $data, ['Local', 'Visita', '3era Camiseta', 'Femenino Local', 'Niño']);
        }
        
        // Color
        if (!$isUpdate || isset($data['color'])) {
            $this->validateRequired('color', $data);
            $this->validateMaxLength('color', $data, 50);
        }
        
        // Precio
        if (!$isUpdate || isset($data['precio'])) {
            $this->validateRequired('precio', $data);
            $this->validatePositiveDecimal('precio', $data);
        }
        
        // Precio oferta (opcional)
        if (isset($data['precio_oferta']) && $data['precio_oferta'] !== null) {
            $this->validatePositiveDecimal('precio_oferta', $data);
        }
        
        // Código producto
        if (!$isUpdate || isset($data['codigo_producto'])) {
            $this->validateRequired('codigo_producto', $data);
            $this->validateMaxLength('codigo_producto', $data, 20);
        }
        
        // Detalles (opcional)
        if (isset($data['detalles'])) {
            $this->validateMaxLength('detalles', $data, 1000);
        }
        
        return $this->errors;
    }
    
    /**
     * Valida los datos de un cliente
     * 
     * @param array $data Datos a validar
     * @param bool $isUpdate Si es una actualización
     * @return array Errores de validación
     */
    public function validateCliente($data, $isUpdate = false) {
        $this->errors = [];
        
        // Nombre comercial
        if (!$isUpdate || isset($data['nombre_comercial'])) {
            $this->validateRequired('nombre_comercial', $data);
            $this->validateMaxLength('nombre_comercial', $data, 100);
        }
        
        // RUT
        if (!$isUpdate || isset($data['rut'])) {
            $this->validateRequired('rut', $data);
            $this->validateRutChileno('rut', $data);
        }
        
        // Dirección
        if (!$isUpdate || isset($data['direccion'])) {
            $this->validateRequired('direccion', $data);
            $this->validateMaxLength('direccion', $data, 200);
        }
        
        // Categoría
        if (!$isUpdate || isset($data['categoria'])) {
            $this->validateEnum('categoria', $data, ['Regular', 'Preferencial'], 'Regular');
        }
        
        // Contacto nombre
        if (!$isUpdate || isset($data['contacto_nombre'])) {
            $this->validateRequired('contacto_nombre', $data);
            $this->validateMaxLength('contacto_nombre', $data, 100);
        }
        
        // Contacto email
        if (!$isUpdate || isset($data['contacto_email'])) {
            $this->validateRequired('contacto_email', $data);
            $this->validateEmail('contacto_email', $data);
        }
        
        // Porcentaje oferta
        if (isset($data['porcentaje_oferta'])) {
            $this->validateNumericRange('porcentaje_oferta', $data, 0, 100);
        }
        
        return $this->errors;
    }
    
    /**
     * Valida los datos de una talla
     * 
     * @param array $data Datos a validar
     * @param bool $isUpdate Si es una actualización
     * @return array Errores de validación
     */
    public function validateTalla($data, $isUpdate = false) {
        $this->errors = [];
        
        // Nombre
        if (!$isUpdate || isset($data['nombre'])) {
            $this->validateRequired('nombre', $data);
            $this->validateMaxLength('nombre', $data, 10);
        }
        
        return $this->errors;
    }
    
    /**
     * Valida que un campo sea requerido
     */
    private function validateRequired($field, $data) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $this->errors[$field] = "El campo {$field} es requerido";
        }
    }
    
    /**
     * Valida la longitud máxima de un campo
     */
    private function validateMaxLength($field, $data, $maxLength) {
        if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
            $this->errors[$field] = "El campo {$field} no puede tener más de {$maxLength} caracteres";
        }
    }
    
    /**
     * Valida que un valor sea de una lista de valores permitidos
     */
    private function validateEnum($field, $data, $allowedValues, $default = null) {
        if (isset($data[$field])) {
            if (!in_array($data[$field], $allowedValues)) {
                $this->errors[$field] = "El campo {$field} debe ser uno de: " . implode(', ', $allowedValues);
            }
        } elseif ($default !== null) {
            $data[$field] = $default;
        }
    }
    
    /**
     * Valida que un valor sea un decimal positivo
     */
    private function validatePositiveDecimal($field, $data) {
        if (isset($data[$field])) {
            if (!is_numeric($data[$field]) || (float)$data[$field] <= 0) {
                $this->errors[$field] = "El campo {$field} debe ser un número positivo";
            }
        }
    }
    
    /**
     * Valida formato de email
     */
    private function validateEmail($field, $data) {
        if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "El campo {$field} debe ser un email válido";
        }
    }
    
    /**
     * Valida RUT chileno
     */
    private function validateRutChileno($field, $data) {
        if (!isset($data[$field])) return;
        
        $rut = $data[$field];
        
        // Formato básico: ########-#
        if (!preg_match('/^\d{7,8}-[\dkK]$/', $rut)) {
            $this->errors[$field] = "El RUT debe tener el formato ########-# (ej: 12345678-9)";
        }
    }
    
    /**
     * Valida que un número esté en un rango específico
     */
    private function validateNumericRange($field, $data, $min, $max) {
        if (isset($data[$field])) {
            $value = (float)$data[$field];
            if ($value < $min || $value > $max) {
                $this->errors[$field] = "El campo {$field} debe estar entre {$min} y {$max}";
            }
        }
    }
    
    /**
     * Valida ID numérico positivo
     */
    public static function validateId($id, $fieldName = 'id') {
        if (!is_numeric($id) || (int)$id <= 0) {
            Response::badRequest("El {$fieldName} debe ser un número positivo");
        }
        return (int)$id;
    }
    
    /**
     * Obtiene datos JSON del cuerpo de la petición
     */
    public static function getJsonInput() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::badRequest('JSON inválido: ' . json_last_error_msg());
        }
        
        return $data ?: [];
    }
} 