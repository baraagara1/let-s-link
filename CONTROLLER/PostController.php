<?php
require_once '../../../config.php';
require_once '../../../model/Post.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

class PostController {

    // Ajouter un post
    public static function addPost($titre, $text, $jointure) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                "INSERT INTO poste (titre, text, jointure) 
                 VALUES (:titre, :text, :jointure)"
            );
            return $query->execute([
                'titre' => $titre,
                'text' => $text,
                'jointure' => $jointure
            ]);
        } catch (PDOException $e) {
            error_log("Erreur SQL: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer tous les posts
    public static function getAllPosts() {
        $sql = "SELECT * FROM poste";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // Récupérer un post par ID
    public static function getPostById($id_p) {
        $sql = "SELECT * FROM poste WHERE id_p = :id_p";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_p', $id_p);
            $query->execute();
            $post = $query->fetch(); // Récupère le résultat sous forme de tableau associatif
            return $post;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // Mettre à jour un post
    public static function updatePost($id_p, $titre, $text, $jointure) {
        $sql = "UPDATE poste SET titre = :titre, text = :text, jointure = :jointure WHERE id_p = :id_p";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_p', $id_p);
            $query->bindValue(':titre', $titre);
            $query->bindValue(':text', $text);
            $query->bindValue(':jointure', $jointure);
            return $query->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // Supprimer un post
    public static function deletePost($id_p) {
        $sql = "DELETE FROM poste WHERE id_p = :id_p";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_p', $id_p);
            $result = $query->execute();
            if (!$result) {
                throw new Exception("Erreur lors de la suppression du post.");
            }
            return $result;
        } catch (Exception $e) {
            // Log detailed error message
            error_log("Erreur de suppression: " . $e->getMessage());
            return false;
        }
    }
}   
?>
