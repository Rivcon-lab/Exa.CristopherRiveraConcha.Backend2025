<?php
/**
 * Configuración de Base de Datos - Patrón Singleton
 * TodoCamisetas API
 * Configuración de base de datos
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // Configuración de base de datos
    private $host = 'localhost';
    private $database = 'todocamisetas_api';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    /**
     * Constructor privado para implementar Singleton
     */
    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}",
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Obtiene la instancia única de la clase (Singleton)
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Obtiene la conexión PDO
     * 
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Previene la clonación del objeto
     */
    private function __clone() {}

    /**
     * Previene la deserialización del objeto
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
} 