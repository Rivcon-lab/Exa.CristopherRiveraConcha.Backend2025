<?php
/**
 * Controlador TallaController
 * Maneja todos los endpoints relacionados con tallas
 */

class TallaController {
    
    /**
     * GET /api/tallas
     * Lista todas las tallas
     */
    public static function index() {
        try {
            $tallas = Talla::all();
            Response::success($tallas);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * GET /api/tallas/{id}
     * Obtiene una talla específica
     */
    public static function show($id) {
        try {
            $id = Validator::validateId($id, 'ID de talla');
            
            $talla = Talla::find($id);
            if (!$talla) {
                Response::notFound('Talla');
            }
            
            Response::success($talla);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * POST /api/tallas
     * Crea una nueva talla
     */
    public static function store() {
        try {
            $data = Validator::getJsonInput();
            
            // Validar datos
            $validator = new Validator();
            $errors = $validator->validateTalla($data);
            
            if (!empty($errors)) {
                Response::badRequest('Datos de validación incorrectos', $errors);
            }
            
            $talla = Talla::create($data);
            Response::created($talla);
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'nombre de la talla ya existe') !== false) {
                Response::conflict($e->getMessage());
            } else {
                Response::serverError($e->getMessage());
            }
        }
    }
    
    /**
     * PUT /api/tallas/{id}
     * Actualiza una talla
     */
    public static function update($id) {
        try {
            $id = Validator::validateId($id, 'ID de talla');
            $data = Validator::getJsonInput();
            
            // Validar datos
            $validator = new Validator();
            $errors = $validator->validateTalla($data, true); // Es actualización
            
            if (!empty($errors)) {
                Response::badRequest('Datos de validación incorrectos', $errors);
            }
            
            $talla = Talla::update($id, $data);
            if (!$talla) {
                Response::notFound('Talla');
            }
            
            Response::success($talla);
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'nombre de la talla ya existe') !== false) {
                Response::conflict($e->getMessage());
            } else {
                Response::serverError($e->getMessage());
            }
        }
    }
    
    /**
     * DELETE /api/tallas/{id}
     * Elimina una talla (con validación de integridad)
     */
    public static function destroy($id) {
        try {
            $id = Validator::validateId($id, 'ID de talla');
            
            $deleted = Talla::delete($id);
            if (!$deleted) {
                Response::notFound('Talla');
            }
            
            Response::success(['message' => 'Talla eliminada correctamente']);
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'camisetas asociadas') !== false) {
                Response::conflict($e->getMessage());
            } else {
                Response::serverError($e->getMessage());
            }
        }
    }
} 