<?php
/**
 * Modelo Camiseta
 * Maneja todas las operaciones de base de datos para camisetas
 */

class Camiseta {
    
    private static function getConnection() {
        return Database::getInstance()->getConnection();
    }
    
    /**
     * Obtiene todas las camisetas con sus tallas
     * 
     * @return array Lista de camisetas con tallas
     */
    public static function all() {
        try {
            $pdo = self::getConnection();
            
            // Obtener todas las camisetas
            $stmt = $pdo->prepare("SELECT * FROM camisetas ORDER BY id");
            $stmt->execute();
            $camisetas = $stmt->fetchAll();
            
            // Para cada camiseta, obtener sus tallas
            foreach ($camisetas as &$camiseta) {
                $camiseta['id'] = (int)$camiseta['id'];
                $camiseta['precio'] = (float)$camiseta['precio'];
                $camiseta['precio_oferta'] = $camiseta['precio_oferta'] ? (float)$camiseta['precio_oferta'] : null;
                $camiseta['tallas'] = self::getTallas($camiseta['id']);
            }
            
            return $camisetas;
            
        } catch (PDOException $e) {
            error_log("Error en Camiseta::all(): " . $e->getMessage());
            throw new Exception("Error al obtener camisetas");
        }
    }
    
    /**
     * Encuentra una camiseta por ID con sus tallas
     * 
     * @param int $id ID de la camiseta
     * @return array|null Datos de la camiseta con tallas
     */
    public static function find($id) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("SELECT * FROM camisetas WHERE id = ?");
            $stmt->execute([$id]);
            $camiseta = $stmt->fetch();
            
            if (!$camiseta) {
                return null;
            }
            
            // Convertir tipos de datos
            $camiseta['id'] = (int)$camiseta['id'];
            $camiseta['precio'] = (float)$camiseta['precio'];
            $camiseta['precio_oferta'] = $camiseta['precio_oferta'] ? (float)$camiseta['precio_oferta'] : null;
            
            // Obtener tallas
            $camiseta['tallas'] = self::getTallas($id);
            
            return $camiseta;
            
        } catch (PDOException $e) {
            error_log("Error en Camiseta::find(): " . $e->getMessage());
            throw new Exception("Error al obtener camiseta");
        }
    }
    
    /**
     * Calcula el precio final para un cliente específico
     * 
     * @param int $camisetaId ID de la camiseta
     * @param int $clienteId ID del cliente
     * @return array|null Precio final calculado
     */
    public static function findWithPricing($camisetaId, $clienteId) {
        try {
            $pdo = self::getConnection();
            
            // Obtener camiseta y cliente en una sola consulta
            $stmt = $pdo->prepare("
                SELECT c.precio, c.precio_oferta, cl.categoria, cl.porcentaje_oferta
                FROM camisetas c
                CROSS JOIN clientes cl
                WHERE c.id = ? AND cl.id = ?
            ");
            
            $stmt->execute([$camisetaId, $clienteId]);
            $data = $stmt->fetch();
            
            if (!$data) {
                return null;
            }
            
            // Aplicar lógica de negocio
            $precioBase = 0;
            
            // 1. Si cliente es Preferencial Y hay precio_oferta, usar precio_oferta
            if ($data['categoria'] === 'Preferencial' && $data['precio_oferta'] !== null) {
                $precioBase = (float)$data['precio_oferta'];
            } else {
                // 2. Sino, usar precio normal
                $precioBase = (float)$data['precio'];
            }
            
            // 3. Aplicar descuento del cliente
            $porcentajeDescuento = (float)$data['porcentaje_oferta'];
            $precioFinal = $precioBase * (1 - ($porcentajeDescuento / 100));
            
            return ['precio_final' => $precioFinal];
            
        } catch (PDOException $e) {
            error_log("Error en Camiseta::findWithPricing(): " . $e->getMessage());
            throw new Exception("Error al calcular precio");
        }
    }
    
    /**
     * Crea una nueva camiseta
     * 
     * @param array $data Datos de la camiseta
     * @return array Camiseta creada
     */
    public static function create($data) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("
                INSERT INTO camisetas (titulo, club, pais, tipo, color, precio, precio_oferta, detalles, codigo_producto)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['club'],
                $data['pais'],
                $data['tipo'],
                $data['color'],
                $data['precio'],
                $data['precio_oferta'] ?? null,
                $data['detalles'] ?? null,
                $data['codigo_producto']
            ]);
            
            $id = $pdo->lastInsertId();
            return self::find($id);
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                throw new Exception("El código de producto ya existe");
            }
            error_log("Error en Camiseta::create(): " . $e->getMessage());
            throw new Exception("Error al crear camiseta");
        }
    }
    
    /**
     * Actualiza una camiseta
     * 
     * @param int $id ID de la camiseta
     * @param array $data Datos a actualizar
     * @return array|null Camiseta actualizada
     */
    public static function update($id, $data) {
        try {
            $pdo = self::getConnection();
            
            // Construir consulta dinámica
            $fields = [];
            $values = [];
            
            $allowedFields = ['titulo', 'club', 'pais', 'tipo', 'color', 'precio', 'precio_oferta', 'detalles', 'codigo_producto'];
            
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
                UPDATE camisetas 
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
                throw new Exception("El código de producto ya existe");
            }
            error_log("Error en Camiseta::update(): " . $e->getMessage());
            throw new Exception("Error al actualizar camiseta");
        }
    }
    
    /**
     * Elimina una camiseta
     * 
     * @param int $id ID de la camiseta
     * @return bool Éxito de la operación
     */
    public static function delete($id) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("DELETE FROM camisetas WHERE id = ?");
            $stmt->execute([$id]);
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en Camiseta::delete(): " . $e->getMessage());
            throw new Exception("Error al eliminar camiseta");
        }
    }
    
    /**
     * Obtiene las tallas de una camiseta
     * 
     * @param int $id ID de la camiseta
     * @return array Lista de tallas con stock
     */
    public static function getTallas($id) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("
                SELECT t.id, t.nombre, ct.stock
                FROM tallas t
                INNER JOIN camiseta_tallas ct ON t.id = ct.talla_id
                WHERE ct.camiseta_id = ?
                ORDER BY t.nombre
            ");
            
            $stmt->execute([$id]);
            $tallas = $stmt->fetchAll();
            
            // Convertir tipos de datos
            foreach ($tallas as &$talla) {
                $talla['id'] = (int)$talla['id'];
                $talla['stock'] = (int)$talla['stock'];
            }
            
            return $tallas;
            
        } catch (PDOException $e) {
            error_log("Error en Camiseta::getTallas(): " . $e->getMessage());
            throw new Exception("Error al obtener tallas");
        }
    }
    
    /**
     * Asigna una talla a una camiseta
     * 
     * @param int $camisetaId ID de la camiseta
     * @param int $tallaId ID de la talla
     * @param int $stock Stock inicial
     * @return array Relación creada
     */
    public static function addTalla($camisetaId, $tallaId, $stock = 0) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("
                INSERT INTO camiseta_tallas (camiseta_id, talla_id, stock)
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$camisetaId, $tallaId, $stock]);
            
            // Obtener la relación creada
            $stmt = $pdo->prepare("
                SELECT t.id, t.nombre, ct.stock
                FROM tallas t
                INNER JOIN camiseta_tallas ct ON t.id = ct.talla_id
                WHERE ct.camiseta_id = ? AND ct.talla_id = ?
            ");
            
            $stmt->execute([$camisetaId, $tallaId]);
            $talla = $stmt->fetch();
            
            $talla['id'] = (int)$talla['id'];
            $talla['stock'] = (int)$talla['stock'];
            
            return $talla;
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                throw new Exception("La talla ya está asignada a esta camiseta");
            }
            error_log("Error en Camiseta::addTalla(): " . $e->getMessage());
            throw new Exception("Error al asignar talla");
        }
    }
    
    /**
     * Actualiza el stock de una talla en una camiseta
     * 
     * @param int $camisetaId ID de la camiseta
     * @param int $tallaId ID de la talla
     * @param int $stock Nuevo stock
     * @return array|null Relación actualizada
     */
    public static function updateTallaStock($camisetaId, $tallaId, $stock) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("
                UPDATE camiseta_tallas 
                SET stock = ?
                WHERE camiseta_id = ? AND talla_id = ?
            ");
            
            $stmt->execute([$stock, $camisetaId, $tallaId]);
            
            if ($stmt->rowCount() === 0) {
                return null;
            }
            
            // Obtener la relación actualizada
            $stmt = $pdo->prepare("
                SELECT t.id, t.nombre, ct.stock
                FROM tallas t
                INNER JOIN camiseta_tallas ct ON t.id = ct.talla_id
                WHERE ct.camiseta_id = ? AND ct.talla_id = ?
            ");
            
            $stmt->execute([$camisetaId, $tallaId]);
            $talla = $stmt->fetch();
            
            $talla['id'] = (int)$talla['id'];
            $talla['stock'] = (int)$talla['stock'];
            
            return $talla;
            
        } catch (PDOException $e) {
            error_log("Error en Camiseta::updateTallaStock(): " . $e->getMessage());
            throw new Exception("Error al actualizar stock");
        }
    }
    
    /**
     * Remueve una talla de una camiseta
     * 
     * @param int $camisetaId ID de la camiseta
     * @param int $tallaId ID de la talla
     * @return bool Éxito de la operación
     */
    public static function removeTalla($camisetaId, $tallaId) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("
                DELETE FROM camiseta_tallas 
                WHERE camiseta_id = ? AND talla_id = ?
            ");
            
            $stmt->execute([$camisetaId, $tallaId]);
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en Camiseta::removeTalla(): " . $e->getMessage());
            throw new Exception("Error al remover talla");
        }
    }
    
    /**
     * Verifica si existe una camiseta por código
     * 
     * @param string $codigo Código del producto
     * @param int|null $excludeId ID a excluir (para updates)
     * @return bool
     */
    public static function existsByCodigo($codigo, $excludeId = null) {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT COUNT(*) FROM camisetas WHERE codigo_producto = ?";
            $params = [$codigo];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en Camiseta::existsByCodigo(): " . $e->getMessage());
            return false;
        }
    }
} 