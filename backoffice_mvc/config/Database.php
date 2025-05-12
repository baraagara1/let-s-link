<?php
// config/Database.php

class Database {
    private $host = 'localhost';
    private $db_name = 'lets_link';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Connexion à la base de données
    public function getConnection() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8";
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("❌ Erreur de connexion : " . $e->getMessage());
            }
        }

        return $this->conn;
    }
}
