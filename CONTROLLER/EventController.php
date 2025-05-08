<?php
require_once '../../../config.php';
require_once '../../../model/Event.php';

class EventController {

    // Liste des événements
    public static function listEvents() {
        $sql = "SELECT * FROM evenements";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();  // <- fetchAll() ici
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
    

    // Ajouter un événement
    // Ajouter un événement
    public function addEvent($event) {
        $db = config::getConnexion();
        try {
            $query = $db->prepare(
                "INSERT INTO evenements (nom_e, id_a, date_e, lieu_e, image)
                 VALUES (:nom_e, :id_a, :date_e, :lieu_e, :image)"
            );
    
            $result = $query->execute([
                'nom_e' => $event->getNom(),
                'id_a' => $event->getIdActivite(), // ici id_a au lieu de type
                'date_e' => $event->getDate(),
                'lieu_e' => $event->getLieu(),
                'image' => $event->getImage(),
            ]);
    
            return $result ? true : "Erreur lors de l'ajout.";
        } catch (PDOException $e) {
            return "Erreur SQL: " . $e->getMessage();
        }
    }
    
    

    // Supprimer un événement
    public function deleteEvent($id_e) {
        $sql = "DELETE FROM evenements WHERE id_e = :id_e";
        $db = config::getConnexion();
        $req = $db->prepare($sql);//prepare securise la requete sql
        $req->bindValue(':id_e', $id_e);
    
        try {
            // Exécuter la requête de suppression
            $req->execute();
    
            // Vérifier si l'événement a été supprimé
            if ($req->rowCount() > 0) {//nombre de lignes affecter
                return true;  // Si l'événement est supprimé, on renvoie true
            } else {
                return "Aucun événement trouvé avec cet ID.";  // Si aucun événement n'a été supprimé
            }
        } catch (Exception $e) {
            // En cas d'erreur, on retourne un message d'erreur détaillé
            return "Erreur lors de la suppression de l'événement : " . $e->getMessage();
        }
    }
    

    // Afficher un événement par son ID
    public function getEventById($id_e) {
        $sql = "SELECT * FROM evenements WHERE id_e = :id_e";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_e', $id_e);
            $query->execute();
            $event = $query->fetch();
            return $event;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Mettre à jour un événement
    public function updateEvent($event, $id_e) {
        $sql = "UPDATE evenements SET nom_e = :nom_e, date_e = :date_e,
                lieu_e = :lieu_e, image = :image WHERE id_e = :id_e";
    
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_e', $id_e);
            $query->bindValue(':nom_e', $event->getNom());
            $query->bindValue(':date_e', $event->getDate());
            $query->bindValue(':lieu_e', $event->getLieu());
            $query->bindValue(':image', $event->getImage());
            $query->execute();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    
    // Liste des événements liés à une activité
public function getEventsByActivite($id_a) {
    $sql = "SELECT * FROM evenements WHERE id_a = :id_a";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->bindValue(':id_a', $id_a, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(); // retourne un tableau d'événements
    } catch (Exception $e) {
        die('Erreur lors de la récupération des événements par activité: ' . $e->getMessage());
    }
}
public function listEventsSorted($orderBy = 'date_e', $direction = 'ASC') {
    $allowedFields = ['nom_e', 'date_e', 'type'];
    $allowedDirections = ['ASC', 'DESC'];

    // Sécurité
    if (!in_array($orderBy, $allowedFields)) $orderBy = 'date_e';
    if (!in_array($direction, $allowedDirections)) $direction = 'ASC';

    $db = config::getConnexion();
    $sql = "
        SELECT e.*, a.nom_a 
        FROM evenements e 
        JOIN activites a ON e.id_a = a.id_a
    ";

    if ($orderBy === 'type') {
        $sql .= " ORDER BY a.nom_a $direction";
    } else {
        $sql .= " ORDER BY e.$orderBy $direction";
    }

    return $db->query($sql)->fetchAll();
}

public function getPaginatedEvents($offset, $limit) {
    $db = config::getConnexion();
    $query = $db->prepare("SELECT * FROM evenements LIMIT :offset, :limit");
    $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll();
}

public function countAllEvents() {
    $db = config::getConnexion();
    return $db->query("SELECT COUNT(*) FROM evenements")->fetchColumn();
}
public function getEventsNotInUserHistory($id_u) {
    $sql = "SELECT * FROM evenements 
            WHERE id_e NOT IN (
                SELECT id_e FROM Participants WHERE id_u = :id_u
            )";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->bindValue(':id_u', $id_u, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    } catch (Exception $e) {
        die('Erreur lors de la récupération des événements non participés: ' . $e->getMessage());
    }
}


public function getUpcomingEvents()
{
    $sql = "SELECT * FROM evenements WHERE date_e >= CURDATE()";
    $db = config::getConnexion();
    try {
        return $db->query($sql)->fetchAll();
    } catch (Exception $e) {
        die('Erreur SQL : ' . $e->getMessage());
    }
}


}
?>
