<?php
require_once 'config/database.php';

class Reservation {
    private $id_res;
    private $covoiturage_id;
    private $user_id;
    private $nb_place_res;
    private $moyen_paiement;
    private $status;

    public function __construct($covoiturage_id, $user_id, $nb_place_res, $moyen_paiement, $status = "En attente", $id_res = null) {
        $this->id_res = $id_res;
        $this->covoiturage_id = $covoiturage_id;
        $this->user_id = $user_id;
        $this->nb_place_res = $nb_place_res;
        $this->moyen_paiement = $moyen_paiement;
        $this->status = $status;
    }

    // Getters
    public function getId() { return $this->id_res; }
    public function getCovoiturageId() { return $this->covoiturage_id; }
    public function getUtilisateurId() { return $this->user_id; }
    public function getPlaces() { return $this->nb_place_res; }
    public function getPaiement() { return $this->moyen_paiement; }
    public function getStatus() { return $this->status; }

    // Setters
    public function setPlaces($val) { $this->nb_place_res = $val; }
    public function setPaiement($val) { $this->moyen_paiement = $val; }
    public function setStatus($val) { $this->status = $val; }

    // Méthodes

    public function save() {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO reservations (covoiturage_id, user_id, nb_place_res, moyen_paiement, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $this->covoiturage_id,
            $this->user_id,
            $this->nb_place_res,
            $this->moyen_paiement,
            $this->status
        ]);
    }

    public static function getAll() {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM reservations ORDER BY id_res DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id_res = ?");
        $stmt->execute([$id]);
    }

    public function update() {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE reservations SET nb_place_res = ?, moyen_paiement = ?, status = ? WHERE id_res = ?");
        $stmt->execute([
            $this->nb_place_res,
            $this->moyen_paiement,
            $this->status,
            $this->id_res
        ]);
    }

    public static function getById($id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id_res = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
