<?php
require_once '../../../config.php';
require_once '../../../MODEL/Participant.php';

class ParticipantController {

    // Ajouter un participant
    public function addParticipant(Participant $participant) {
        $db = config::getConnexion();
        try {
            $sql = "INSERT INTO Participants (id_e, id) VALUES (:id_e, :id)";
            $query = $db->prepare($sql);
            return $query->execute([
                ':id_e' => $participant->getIdEvenement(),
                ':id' => $participant->getIdUtilisateur()
            ]);
        } catch (PDOException $e) {
            die('Erreur SQL : ' . $e->getMessage());
        }
    }

    // Vérifie si l'utilisateur participe déjà à un événement
    public function checkIfAlreadyParticipating($id, $id_e) {
        $db = config::getConnexion();
        try {
            $sql = "SELECT COUNT(*) FROM Participants WHERE id = :id AND id_e = :id_e";
            $query = $db->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':id_e' => $id_e
            ]);
            return $query->fetchColumn() > 0;
        } catch (PDOException $e) {
            die('Erreur SQL : ' . $e->getMessage());
        }
    }

    // Récupérer tous les événements d’un utilisateur
    public function getEventsByUser($id) {
        $db = config::getConnexion();
        try {
            $sql = "SELECT e.* 
                    FROM evenements e
                    JOIN Participants p ON e.id_e = p.id_e
                    WHERE p.id = :id";
            $query = $db->prepare($sql);
            $query->execute([':id' => $id]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            die('Erreur SQL : ' . $e->getMessage());
        }
    }

}
