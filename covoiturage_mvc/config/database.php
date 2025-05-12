<?php
class Database {
    public static function connect() {
        try {
            return new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
        } catch (PDOException $e) {
            die("❌ Erreur de connexion à la base : " . $e->getMessage());
        }
    }
}
