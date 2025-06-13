<?php
/**
 * Controlador CamisetaController
 * Maneja todos los endpoints relacionados con camisetas
 */

class CamisetaController {
    
    /**
     * GET /api/camisetas
     * Lista todas las camisetas con tallas
     */
    public static function index() {
        try {
            $camisetas = Camiseta::all();
            Response::success($camisetas);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * GET /api/camisetas/{id}
     * Obtiene una camiseta específica con tallas
     */
    public static function show($id) {
        try {
            $id = Validator::validateId($id, 'ID de camiseta');
            
            $camiseta = Camiseta::find($id);
            if (!$camiseta) {
                Response::notFound('Camiseta');
            }
            
            Response::success($camiseta);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * GET /api/camisetas/{id}/precio/{cliente_id}
     * Obtiene el precio final calculado para un cliente específico
     */
    public static function getPrecioFinal($id, $cliente_id) {
        try {
            $id = Validator::validateId($id, 'ID de camiseta');
            $cliente_id = Validator::validateId($cliente_id, 'ID de cliente');
            
            $precio = Camiseta::findWithPricing($id, $cliente_id);
            if (!$precio) {
                Response::notFound('Camiseta o cliente');
            }
            
            Response::success($precio);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * POST /api/camisetas
     * Crea una nueva camiseta
     */
    public static function store() {
        try {
            $data = Validator::getJsonInput();
            
            // Validar datos
            $validator = new Validator();
            $errors = $validator->validateCamiseta($data);
            
            if (!empty($errors)) {
                Response::badRequest('Datos de validación incorrectos', $errors);
            }
            
            $camiseta = Camiseta::create($data);
            Response::created($camiseta);
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'código de producto') !== false) {
                Response::conflict($e->getMessage());
            } else {
                Response::serverError($e->getMessage());
            }
        }
    }
    
    /**
     * PUT /api/camisetas/{id}
     * Actualiza una camiseta
     */
    public static function update($id) {
        try {
            $id = Validator::validateId($id, 'ID de camiseta');
            $data = Validator::getJsonInput();
            
            // Validar datos
            $validator = new Validator();
            $errors = $validator->validateCamiseta($data, true); // Es actualización
            
            if (!empty($errors)) {
                Response::badRequest('Datos de validación incorrectos', $errors);
            }
            
            $camiseta = Camiseta::update($id, $data);
            if (!$camiseta) {
                Response::notFound('Camiseta');
            }
            
            Response::success($camiseta);
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'código de producto') !== false) {
                Response::conflict($e->getMessage());
            } else {
                Response::serverError($e->getMessage());
            }
        }
    }
    
    /**
     * DELETE /api/camisetas/{id}
     * Elimina una camiseta
     */
    public static function destroy($id) {
        try {
            $id = Validator::validateId($id, 'ID de camiseta');
            
            $deleted = Camiseta::delete($id);
            if (!$deleted) {
                Response::notFound('Camiseta');
            }
            
            Response::success(['message' => 'Camiseta eliminada correctamente']);
            
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * GET /api/camisetas/{id}/tallas
     * Obtiene las tallas de una camiseta
     */
    public static function getTallas($id) {
        try {
            $id = Validator::validateId($id, 'ID de camiseta');
            
            // Verificar que la camiseta existe
            $camiseta = Camiseta::find($id);
            if (!$camiseta) {
                Response::notFound('Camiseta');
            }
            
            $tallas = Camiseta::getTallas($id);
            Response::success($tallas);
            
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * POST /api/camisetas/{id}/tallas
     * Asigna una talla a una camiseta
     */
    public static function addTalla($id) {
        try {
            $id = Validator::validateId($id, 'ID de camiseta');
            $data = Validator::getJsonInput();
            
            // Validar datos requeridos
            if (!isset($data['talla_id'])) {
                Response::badRequest('El talla_id es requerido');
            }
            
            $tallaId = Validator::validateId($data['talla_id'], 'ID de talla');
            $stock = isset($data['stock']) ? (int)$data['stock'] : 0;
            
            if ($stock < 0) {
                Response::badRequest('El stock no puede ser negativo');
            }
            
            // Verificar que la camiseta existe
            $camiseta = Camiseta::find($id);
            if (!$camiseta) {
                Response::notFound('Camiseta');
            }
            
            // Verificar que la talla existe
            $talla = Talla::find($tallaId);
            if (!$talla) {
                Response::notFound('Talla');
            }
            
            $relacion = Camiseta::addTalla($id, $tallaId, $stock);
            Response::created($relacion);
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'ya está asignada') !== false) {
                Response::conflict($e->getMessage());
            } else {
                Response::serverError($e->getMessage());
            }
        }
    }
    
    /**
     * PUT /api/camisetas/{camiseta_id}/tallas/{talla_id}
     * Actualiza el stock de una talla en una camiseta
     */
    public static function updateTallaStock($camiseta_id, $talla_id) {
        try {
            $camiseta_id = Validator::validateId($camiseta_id, 'ID de camiseta');
            $talla_id = Validator::validateId($talla_id, 'ID de talla');
            $data = Validator::getJsonInput();
            
            // Validar stock
            if (!isset($data['stock'])) {
                Response::badRequest('El stock es requerido');
            }
            
            $stock = (int)$data['stock'];
            if ($stock < 0) {
                Response::badRequest('El stock no puede ser negativo');
            }
            
            $relacion = Camiseta::updateTallaStock($camiseta_id, $talla_id, $stock);
            if (!$relacion) {
                Response::notFound('Relación camiseta-talla');
            }
            
            Response::success($relacion);
            
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * DELETE /api/camisetas/{camiseta_id}/tallas/{talla_id}
     * Remueve una talla de una camiseta
     */
    public static function removeTalla($camiseta_id, $talla_id) {
        try {
            $camiseta_id = Validator::validateId($camiseta_id, 'ID de camiseta');
            $talla_id = Validator::validateId($talla_id, 'ID de talla');
            
            $removed = Camiseta::removeTalla($camiseta_id, $talla_id);
            if (!$removed) {
                Response::notFound('Relación camiseta-talla');
            }
            
            Response::success(['message' => 'Talla removida de la camiseta correctamente']);
            
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
} 