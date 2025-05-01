
<?php
require_once '../../../config.php';
require_once '../../../model/Commentaire.php';

class CommentaireController {

    // Ajouter un commentaire
    public function addCommentaire($contenu, $id_p, $id_u) {
        $db = config::getConnexion();
        try {
            // Créer un objet commentaire
            $commentaire = new Commentaire($contenu, $id_p, $id_u);

            // Préparer la requête d'ajout sans l'ID
            $query = $db->prepare(
                "INSERT INTO commentaires (contenu, date_c, id_p, id_u) 
                 VALUES (:contenu, CURRENT_TIMESTAMP(), :id_p, :id_u)"
            );

            // Exécuter la requête avec les paramètres
            $result = $query->execute([
                'contenu' => $commentaire->getContenu(),
                'id_p' => $commentaire->getPostId(),
                'id_u' => $commentaire->getUserId()
            ]);

            if ($result) {
                return true;  // Ajout réussi
            } else {
                return "Erreur lors de l'ajout du commentaire.";  // Échec de l'ajout
            }

        } catch (PDOException $e) {
            return "Erreur SQL: " . $e->getMessage();  // Message d'erreur si exception
        }
    }

    // Récupérer tous les commentaires d'un post
    public function getCommentairesByPostId($id_p) {
        $sql = "SELECT * FROM commentaires WHERE id_p = :id_p";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_p', $id_p);
            $query->execute();
            $commentaires = $query->fetchAll(); // Récupère le résultat sous forme de tableau associatif
            return $commentaires;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // Supprimer un commentaire
    public function deleteCommentaire($id_c) {
        $sql = "DELETE FROM commentaires WHERE id_c = :id_c";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_c', $id_c);

        try {
            // Exécuter la requête de suppression
            $req->execute();

            // Vérifier si le commentaire a été supprimé
            if ($req->rowCount() > 0) {
                return true;  // Si le commentaire est supprimé, on renvoie true
            } else {
                return "Aucun commentaire trouvé avec cet ID.";  // Si aucun commentaire n'a été supprimé
            }
        } catch (Exception $e) {
            // En cas d'erreur, on retourne un message d'erreur détaillé
            return "Erreur lors de la suppression du commentaire : " . $e->getMessage();
        }
    }

    // Afficher un commentaire par son ID
    public function getCommentaireById($id_c) {
        $sql = "SELECT * FROM commentaires WHERE id_c = :id_c";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_c', $id_c);
            $query->execute();
            $commentaire = $query->fetch();
            return $commentaire;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Mettre à jour un commentaire
    public function updateCommentaire($contenu, $id_c) {
        $sql = "UPDATE commentaires SET contenu = :contenu WHERE id_c = :id_c";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_c', $id_c);
            $query->bindValue(':contenu', $contenu);  // Utilisation du contenu directement
            $query->execute();
    
            // Vérifier si la mise à jour a eu lieu
            if ($query->rowCount() > 0) {
                return true;  // La mise à jour a réussi
            } else {
                return "Aucune modification n'a été effectuée.";
            }
    
        } catch (Exception $e) {
            // Afficher le message d'erreur complet pour une meilleure détection du problème
            return "Erreur lors de la mise à jour du commentaire: " . $e->getMessage();
        }
    }
    public function getCommentairesWithPostTitles() {
        $db = config::getConnexion();
        $sql = "SELECT commentaires.*, poste.titre AS post_title 
                FROM commentaires
                INNER JOIN poste ON commentaires.id_p = poste.id_p";
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $commentaires = $query->fetchAll(); // Récupère les résultats
            return $commentaires;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    // Ajoutez ces deux nouvelles méthodes seulement
public function getPaginatedCommentaires($postId, $offset = 0, $limit = 3) {
    $sql = "SELECT * FROM commentaires WHERE id_p = ? ORDER BY date_c DESC LIMIT ?, ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(1, $postId, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function countCommentairesByPostId($postId) {
    $sql = "SELECT COUNT(*) as total FROM commentaires WHERE id_p = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$postId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}
}   

?>
