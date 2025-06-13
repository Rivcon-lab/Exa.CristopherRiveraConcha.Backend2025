<?php
/**
 * Modelo Cliente
 * Maneja todas las operaciones de base de datos para clientes
 */

class Cliente {
    
    private static function getConnection() {
        return Database::getInstance()->getConnection();
    }
    
    /**
     * Obtiene todos los clientes
     * 
     * @return array Lista de clientes
     */
    public static function all() {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("SELECT * FROM clientes ORDER BY id");
            $stmt->execute();
            $clientes = $stmt->fetchAll();
            
            // Convertir tipos de datos
            foreach ($clientes as &$cliente) {
                $cliente['id'] = (int)$cliente['id'];
                $cliente['porcentaje_oferta'] = (float)$cliente['porcentaje_oferta'];
            }
            
            return $clientes;
            
        } catch (PDOException $e) {
            error_log("Error en Cliente::all(): " . $e->getMessage());
            throw new Exception("Error al obtener clientes");
        }
    }
    
    /**
     * Encuentra un cliente por ID
     * 
     * @param int $id ID del cliente
     * @return array|null Datos del cliente
     */
    public static function find($id) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            $cliente = $stmt->fetch();
            
            if (!$cliente) {
                return null;
            }
            
            // Convertir tipos de datos
            $cliente['id'] = (int)$cliente['id'];
            $cliente['porcentaje_oferta'] = (float)$cliente['porcentaje_oferta'];
            
            return $cliente;
            
        } catch (PDOException $e) {
            error_log("Error en Cliente::find(): " . $e->getMessage());
            throw new Exception("Error al obtener cliente");
        }
    }
    
    /**
     * Crea un nuevo cliente
     * 
     * @param array $data Datos del cliente
     * @return array Cliente creado
     */
    public static function create($data) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("
                INSERT INTO clientes (nombre_comercial, rut, direccion, categoria, contacto_nombre, contacto_email, porcentaje_oferta)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['nombre_comercial'],
                $data['rut'],
                $data['direccion'],
                $data['categoria'] ?? 'Regular',
                $data['contacto_nombre'],
                $data['contacto_email'],
                $data['porcentaje_oferta'] ?? 0.00
            ]);
            
            $id = $pdo->lastInsertId();
            return self::find($id);
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                throw new Exception("El RUT ya existe");
            }
            error_log("Error en Cliente::create(): " . $e->getMessage());
            throw new Exception("Error al crear cliente");
        }
    }
    
    /**
     * Actualiza un cliente
     * 
     * @param int $id ID del cliente
     * @param array $data Datos a actualizar
     * @return array|null Cliente actualizado
     */
    public static function update($id, $data) {
        try {
            $pdo = self::getConnection();
            
            // Construir consulta dinámica
            $fields = [];
            $values = [];
            
            $allowedFields = ['nombre_comercial', 'rut', 'direccion', 'categoria', 'contacto_nombre', 'contacto_email', 'porcentaje_oferta'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                throw new Exception("No hay campos para actualizar");
            }
            
            $values[] = $id;
            
            $stmt = $pdo->prepare("
                UPDATE clientes 
                SET " . implode(', ', $fields) . "
                WHERE id = ?
            ");
            
            $stmt->execute($values);
            
            if ($stmt->rowCount() === 0) {
                return null;
            }
            
            return self::find($id);
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                throw new Exception("El RUT ya existe");
            }
            error_log("Error en Cliente::update(): " . $e->getMessage());
            throw new Exception("Error al actualizar cliente");
        }
    }
    
    /**
     * Elimina un cliente (con validación de integridad)
     * 
     * @param int $id ID del cliente
     * @return bool Éxito de la operación
     * @throws Exception Si tiene camisetas asociadas
     */
    public static function delete($id) {
        try {
            $pdo = self::getConnection();
            
            // Verificar si tiene camisetas asociadas
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM camiseta_tallas ct
                INNER JOIN camisetas c ON ct.camiseta_id = c.id
                WHERE EXISTS (SELECT 1 FROM clientes cl WHERE cl.id = ?)
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                throw new Exception("No se puede eliminar cliente con camisetas asociadas");
            }
            
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en Cliente::delete(): " . $e->getMessage());
            throw new Exception("Error al eliminar cliente");
        }
    }
    
    /**
     * Obtiene las camisetas disponibles para un cliente
     * 
     * @param int $clienteId ID del cliente
     * @return array Lista de camisetas con precios calculados
     */
    public static function getCamisetas($clienteId) {
        try {
            $pdo = self::getConnection();
            
            // Verificar que el cliente existe
            $cliente = self::find($clienteId);
            if (!$cliente) {
                return null;
            }
            
            // Obtener todas las camisetas
            $camisetas = Camiseta::all();
            
            // Calcular precio para cada camiseta según el cliente
            foreach ($camisetas as &$camiseta) {
                $precioBase = 0;
                
                // Aplicar lógica de negocio
                if ($cliente['categoria'] === 'Preferencial' && $camiseta['precio_oferta'] !== null) {
                    $precioBase = $camiseta['precio_oferta'];
                } else {
                    $precioBase = $camiseta['precio'];
                }
                
                // Aplicar descuento del cliente
                $precioFinal = $precioBase * (1 - ($cliente['porcentaje_oferta'] / 100));
                $camiseta['precio_para_cliente'] = $precioFinal;
            }
            
            return $camisetas;
            
        } catch (PDOException $e) {
            error_log("Error en Cliente::getCamisetas(): " . $e->getMessage());
            throw new Exception("Error al obtener camisetas del cliente");
        }
    }
    
    /**
     * Verifica si existe un cliente por RUT
     * 
     * @param string $rut RUT del cliente
     * @param int|null $excludeId ID a excluir (para updates)
     * @return bool
     */
    public static function existsByRut($rut, $excludeId = null) {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT COUNT(*) FROM clientes WHERE rut = ?";
            $params = [$rut];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en Cliente::existsByRut(): " . $e->getMessage());
            return false;
        }
    }
} 