<?php
/**
 * Modelo Talla
 * Maneja todas las operaciones de base de datos para tallas
 */

class Talla {
    
    private static function getConnection() {
        return Database::getInstance()->getConnection();
    }
    
    /**
     * Obtiene todas las tallas
     * 
     * @return array Lista de tallas
     */
    public static function all() {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("SELECT * FROM tallas ORDER BY nombre");
            $stmt->execute();
            $tallas = $stmt->fetchAll();
            
            // Convertir tipos de datos
            foreach ($tallas as &$talla) {
                $talla['id'] = (int)$talla['id'];
            }
            
            return $tallas;
            
        } catch (PDOException $e) {
            error_log("Error en Talla::all(): " . $e->getMessage());
            throw new Exception("Error al obtener tallas");
        }
    }
    
    /**
     * Encuentra una talla por ID
     * 
     * @param int $id ID de la talla
     * @return array|null Datos de la talla
     */
    public static function find($id) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("SELECT * FROM tallas WHERE id = ?");
            $stmt->execute([$id]);
            $talla = $stmt->fetch();
            
            if (!$talla) {
                return null;
            }
            
            // Convertir tipos de datos
            $talla['id'] = (int)$talla['id'];
            
            return $talla;
            
        } catch (PDOException $e) {
            error_log("Error en Talla::find(): " . $e->getMessage());
            throw new Exception("Error al obtener talla");
        }
    }
    
    /**
     * Crea una nueva talla
     * 
     * @param array $data Datos de la talla
     * @return array Talla creada
     */
    public static function create($data) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("
                INSERT INTO tallas (nombre)
                VALUES (?)
            ");
            
            $stmt->execute([$data['nombre']]);
            
            $id = $pdo->lastInsertId();
            return self::find($id);
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                throw new Exception("El nombre de la talla ya existe");
            }
            error_log("Error en Talla::create(): " . $e->getMessage());
            throw new Exception("Error al crear talla");
        }
    }
    
    /**
     * Actualiza una talla
     * 
     * @param int $id ID de la talla
     * @param array $data Datos a actualizar
     * @return array|null Talla actualizada
     */
    public static function update($id, $data) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("
                UPDATE tallas 
                SET nombre = ?
                WHERE id = ?
            ");
            
            $stmt->execute([$data['nombre'], $id]);
            
            if ($stmt->rowCount() === 0) {
                return null;
            }
            
            return self::find($id);
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                throw new Exception("El nombre de la talla ya existe");
            }
            error_log("Error en Talla::update(): " . $e->getMessage());
            throw new Exception("Error al actualizar talla");
        }
    }
    
    /**
     * Elimina una talla (con validación de integridad)
     * 
     * @param int $id ID de la talla
     * @return bool Éxito de la operación
     * @throws Exception Si tiene camisetas asociadas
     */
    public static function delete($id) {
        try {
            $pdo = self::getConnection();
            
            // Verificar si tiene camisetas asociadas
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM camiseta_tallas 
                WHERE talla_id = ?
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                throw new Exception("No se puede eliminar talla con camisetas asociadas");
            }
            
            $stmt = $pdo->prepare("DELETE FROM tallas WHERE id = ?");
            $stmt->execute([$id]);
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en Talla::delete(): " . $e->getMessage());
            throw new Exception("Error al eliminar talla");
        }
    }
    
    /**
     * Verifica si existe una talla por nombre
     * 
     * @param string $nombre Nombre de la talla
     * @param int|null $excludeId ID a excluir (para updates)
     * @return bool
     */
    public static function existsByNombre($nombre, $excludeId = null) {
        try {
            $pdo = self::getConnection();
            
            $sql = "SELECT COUNT(*) FROM tallas WHERE nombre = ?";
            $params = [$nombre];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en Talla::existsByNombre(): " . $e->getMessage());
            return false;
        }
    }
} 