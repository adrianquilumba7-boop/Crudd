<?php
class Database {
    private $db;
    
    public function __construct() {
        try {
            $this->db = new PDO('sqlite:crud.sqlite');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTable();
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    private function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS usuarios (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    nombre TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    telefono TEXT,
                    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
                  )";
        $this->db->exec($query);
    }
    
    public function getConnection() {
        return $this->db;
    }
}
?>