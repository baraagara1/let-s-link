<?php
require_once 'C:\xampp\htdocs\mon_project_web\config.php';
require_once 'C:\xampp\htdocs\mon_project_web\model\utilisateur.php';

class UtilisateurC
{
    public function ajouterUtilisateur(Utilisateur $u)
    {
        $sql = "INSERT INTO usser (nom, prenom, email, telephone, adresse, mot, role)
                VALUES (:nom, :prenom, :email, :telephone, :adresse, :mot, :role)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $u->getNom(),
                'prenom' => $u->getPrenom(),
                'email' => $u->getEmail(),
                'telephone' => $u->getTelephone(),
                'adresse' => $u->getAdresse(),
                'mot' => $u->getMotpasse(), // Mot de passe en clair
                'role' => $u->getRole()
            ]);
        } catch (PDOException $e) {
            die('Erreur lors de l\'ajout : ' . $e->getMessage());
        }
    }

    public function afficherUtilisateurs()
    {
        $sql = "SELECT * FROM usser";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll();
        } catch (PDOException $e) {
            die('Erreur lors de la récupération : ' . $e->getMessage());
        }
    }

    public function modifierUtilisateur($id, Utilisateur $u)
    {
        $sql = "UPDATE usser SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, adresse = :adresse, mot = :mot, role = :role WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $u->getNom(),
                'prenom' => $u->getPrenom(),
                'email' => $u->getEmail(),
                'telephone' => $u->getTelephone(),
                'adresse' => $u->getAdresse(),
                'mot' => $u->getMotpasse(), // Mot de passe en clair
                'role' => $u->getRole(),
                'id' => $id
            ]);
        } catch (PDOException $e) {
            die('Erreur lors de la modification : ' . $e->getMessage());
        }
    }

    public function supprimerUtilisateur($id) {
        $db = config::getConnexion();
        
        try {
            // Vérifier d'abord si l'utilisateur existe
            $check_sql = "SELECT id FROM usser WHERE id = :id";
            $check_query = $db->prepare($check_sql);
            $check_query->bindValue(':id', $id);
            $check_query->execute();
            
            if ($check_query->rowCount() == 0) {
                throw new Exception("L'utilisateur n'existe pas");
            }
    
            // Supprimer l'utilisateur
            $delete_sql = "DELETE FROM usser WHERE id = :id";
            $delete_query = $db->prepare($delete_sql);
            $delete_query->bindValue(':id', $id);
            
            if (!$delete_query->execute()) {
                throw new Exception("Erreur lors de la suppression de l'utilisateur");
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur de base de données : " . $e->getMessage());
        }
    }

    public function verifierConnexion($email, $motdepasse)
    {
        $sql = "SELECT * FROM usser WHERE email = :email";
        $db = config::getConnexion();
    
        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => $email]);
            $user = $query->fetch();
    
            if ($user && $motdepasse === $user['mot']) { // Comparaison directe des mots de passe
                return $user['role'];
            }
    
            return false;
        } catch (PDOException $e) {
            die('Erreur lors de la vérification de connexion: ' . $e->getMessage());
        }
    }
}
?>