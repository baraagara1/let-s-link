<?php
require_once 'config/Database.php';

class Demande {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM demandes_suppression ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function changerStatut($id, $action) {
        if (!in_array($action, ['accepter', 'refuser'])) return false;
    
        $statutFinal = ($action === 'accepter') ? 'Acceptée' : 'Refusée';
    
        if ($action === 'accepter') {
            // 1. Récupérer l’ID de la réservation
            $stmt = $this->conn->prepare("SELECT reservation_id FROM demandes_suppression WHERE id = ?");
            $stmt->execute([$id]);
            $resId = $stmt->fetchColumn();
    
            if ($resId) {
                // 2. Récupérer les infos de la réservation
                $stmt = $this->conn->prepare("SELECT covoiturage_id, nb_place_res FROM reservations WHERE id_res = ?");
                $stmt->execute([$resId]);
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($res) {
                    $idCov = $res['covoiturage_id'];
                    $nbPlaces = $res['nb_place_res'];
    
                    // 3. Réincrémenter les places disponibles
                    $stmt = $this->conn->prepare("UPDATE covoiturage SET place_dispo = place_dispo + ? WHERE id_cov = ?");
                    $stmt->execute([$nbPlaces, $idCov]);
    
                    // 4. Supprimer la réservation
                    $this->conn->prepare("DELETE FROM reservations WHERE id_res = ?")->execute([$resId]);
                }
            }
        }
    
        // 5. Mettre à jour le statut de la demande
        $stmt = $this->conn->prepare("UPDATE demandes_suppression SET statut = ? WHERE id = ?");
        return $stmt->execute([$statutFinal, $id]);
    }
    
    
}
