<?php
require_once '../../../config.php';
require_once '../../../model/Activite.php';

class ActiviteController {

    // Liste des activités
    public static function listActivites() {
        $sql = "SELECT * FROM activites";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();  // <- fetchAll() ici aussi
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
    
    

    // Ajouter une activité
    // Ajouter une activité
public function addActivite($activite) {
    $db = config::getConnexion();
    try {
        $query = $db->prepare(
            "INSERT INTO activites (nom_a, description, image, id)
             VALUES (:nom_a, :description, :image, :id)"
        );

        $result = $query->execute([
            'nom_a' => $activite->getNom(),
            'description' => $activite->getDescription(),
            'image' => $activite->getImage(),
            'id' => 0 // ID utilisateur fixé à 0 temporairement
        ]);

        return $result ? true : "Erreur lors de l'ajout de l'activité.";
    } catch (PDOException $e) {
        return "Erreur SQL: " . $e->getMessage();
    }
}


    // Supprimer une activité
    public function deleteActivite($id_a) {
        $sql = "DELETE FROM activites WHERE id_a = :id_a";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_a', $id_a);

        try {
            $req->execute();
            return ($req->rowCount() > 0) ? true : "Aucune activité trouvée avec cet ID.";
        } catch (Exception $e) {
            return "Erreur lors de la suppression de l'activité : " . $e->getMessage();
        }
    }

    // Récupérer une activité par ID
    public function getActiviteById($id_a) {
        $sql = "SELECT * FROM activites WHERE id_a = :id_a";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_a', $id_a);
            $query->execute();
            return $query->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Mettre à jour une activité
    public function updateActivite($activite, $id_a) {
        $sql = "UPDATE activites SET nom_a = :nom_a, description = :description, 
                image = :image  WHERE id_a = :id_a";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_a', $id_a);
            $query->bindValue(':nom_a', $activite->getNom());
            $query->bindValue(':description', $activite->getDescription());
            $query->bindValue(':image', $activite->getImage());
           
            $query->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    public function getAllActivites() {
        $db = config::getConnexion();
        try {
            $query = $db->query("SELECT * FROM activites");
            return $query->fetchAll(); // 👈 important : fetchAll()
        } catch (PDOException $e) {
            die("Erreur lors de la récupération des activités : " . $e->getMessage());
        }
    }
    public static function searchActivitesByKeyword($motCle) {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->prepare("SELECT * FROM activite WHERE nom_a LIKE :keyword OR description_a LIKE :keyword");
            $stmt->execute([':keyword' => "%$motCle%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    
}
?>
