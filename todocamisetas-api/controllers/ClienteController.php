<?php
/**
 * Controlador ClienteController
 * Maneja todos los endpoints relacionados con clientes
 */

class ClienteController {
    
    /**
     * GET /api/clientes
     * Lista todos los clientes
     */
    public static function index() {
        try {
            $clientes = Cliente::all();
            Response::success($clientes);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * GET /api/clientes/{id}
     * Obtiene un cliente específico
     */
    public static function show($id) {
        try {
            $id = Validator::validateId($id, 'ID de cliente');
            
            $cliente = Cliente::find($id);
            if (!$cliente) {
                Response::notFound('Cliente');
            }
            
            Response::success($cliente);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * POST /api/clientes
     * Crea un nuevo cliente
     */
    public static function store() {
        try {
            $data = Validator::getJsonInput();
            
            // Validar datos
            $validator = new Validator();
            $errors = $validator->validateCliente($data);
            
            if (!empty($errors)) {
                Response::badRequest('Datos de validación incorrectos', $errors);
            }
            
            $cliente = Cliente::create($data);
            Response::created($cliente);
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'RUT ya existe') !== false) {
                Response::conflict($e->getMessage());
            } else {
                Response::serverError($e->getMessage());
            }
        }
    }
    
    /**
     * PUT /api/clientes/{id}
     * Actualiza un cliente
     */
    public static function update($id) {
        try {
            $id = Validator::validateId($id, 'ID de cliente');
            $data = Validator::getJsonInput();
            
            // Validar datos
            $validator = new Validator();
            $errors = $validator->validateCliente($data, true); // Es actualización
            
            if (!empty($errors)) {
                Response::badRequest('Datos de validación incorrectos', $errors);
            }
            
            $cliente = Cliente::update($id, $data);
            if (!$cliente) {
                Response::notFound('Cliente');
            }
            
            Response::success($cliente);
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'RUT ya existe') !== false) {
                Response::conflict($e->getMessage());
            } else {
                Response::serverError($e->getMessage());
            }
        }
    }
    
    /**
     * DELETE /api/clientes/{id}
     * Elimina un cliente (con validación de integridad)
     */
    public static function destroy($id) {
        try {
            $id = Validator::validateId($id, 'ID de cliente');
            
            $deleted = Cliente::delete($id);
            if (!$deleted) {
                Response::notFound('Cliente');
            }
            
            Response::success(['message' => 'Cliente eliminado correctamente']);
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'camisetas asociadas') !== false) {
                Response::conflict($e->getMessage());
            } else {
                Response::serverError($e->getMessage());
            }
        }
    }
    
    /**
     * GET /api/clientes/{id}/camisetas
     * Obtiene las camisetas disponibles para un cliente con precios calculados
     */
    public static function getCamisetas($id) {
        try {
            $id = Validator::validateId($id, 'ID de cliente');
            
            $camisetas = Cliente::getCamisetas($id);
            if ($camisetas === null) {
                Response::notFound('Cliente');
            }
            
            Response::success($camisetas);
            
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
} 