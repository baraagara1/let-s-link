<?php
require_once __DIR__ . '/../config.php';

class FavorisModel {
    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    public function addFavori($id, $id_e) {
        $query = $this->db->prepare("INSERT IGNORE INTO favoris (id, id_e) VALUES (?, ?)");
        $query->execute([$id, $id_e]);
    }

    public function removeFavori($id, $id_e) {
        $query = $this->db->prepare("DELETE FROM favoris WHERE id = ? AND id_e = ?");
        $query->execute([$id, $id_e]);
    }

    public function isFavori($id, $id_e) {
        $query = $this->db->prepare("SELECT COUNT(*) FROM favoris WHERE id = ? AND id_e = ?");
        $query->execute([$id, $id_e]);
        return $query->fetchColumn() > 0;
    }

    public function getFavorisByUser($id) {
        $query = $this->db->prepare("SELECT e.* FROM evenements e
            JOIN favoris f ON e.id_e = f.id_e
            WHERE f.id = ?");
        $query->execute([$id]);
        return $query->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les événements favoris
    }
    
}
