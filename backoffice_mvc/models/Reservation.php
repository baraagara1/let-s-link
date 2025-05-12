<?php
require_once 'config/Database.php';

class Reservation {
    private $pdo; // ✅ On remplace $conn par $pdo

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection(); // ✅ Connexion PDO
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations ORDER BY id_res DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations WHERE id_res = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reservations WHERE id_res = ?");
        return $stmt->execute([$id]);
    }

    public function modifier($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE reservations SET
                moyen_paiement = ?,
                user_id = ?,
                covoiturage_id = ?,
                status = ?,
                nb_place_res = ?
            WHERE id_res = ?
        ");
        return $stmt->execute([
            $data['moyen_paiement'],
            $data['user_id'],
            $data['covoiturage_id'],
            $data['status'],
            $data['nb_place_res'],
            $id
        ]);
    }

    public function getAllReservations() {
        $stmt = $this->pdo->query("SELECT * FROM reservations ORDER BY id_res DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
