<?php
require_once 'config/Database.php';

class Covoiturage {
    private $pdo; // ✅ nom unifié (plus de $conn)

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection(); // ✅ on stocke dans $pdo
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM covoiturage ORDER BY date ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM covoiturage WHERE id_cov = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function ajouter($data) {
        $stmt = $this->pdo->prepare("INSERT INTO covoiturage (lieu_depart, destination, date, heure_depart, place_dispo, prix_c, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['lieu_depart'],
            $data['destination'],
            $data['date'],
            $data['heure_depart'],
            $data['place_dispo'],
            $data['prix_c'],
            $data['user_id']
        ]);
    }

    public function modifier($id, $data) {
        $sql = "UPDATE covoiturage SET
                  lieu_depart = ?, destination = ?, date = ?, heure_depart = ?,
                  place_dispo = ?, prix_c = ?, user_id = ?
                WHERE id_cov = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['lieu_depart'],
            $data['destination'],
            $data['date'],
            $data['heure_depart'],
            $data['place_dispo'],
            $data['prix_c'],
            $data['user_id'],
            $id
        ]);
    }

    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM covoiturage WHERE id_cov = ?");
        return $stmt->execute([$id]);
    }

    public function getAllCovoiturages() {
        $stmt = $this->pdo->query("SELECT * FROM covoiturage ORDER BY id_cov DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
