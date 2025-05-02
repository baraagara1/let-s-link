<?php
require_once 'C:\xampp\htdocs\mon_project_web\config.php';
require_once 'C:\xampp\htdocs\mon_project_web\model\utilisateur.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:/xampp/htdocs/mon_project_web/PHPMailer-master/src/Exception.php';
require 'C:/xampp/htdocs/mon_project_web/PHPMailer-master/src/PHPMailer.php';
require 'C:/xampp/htdocs/mon_project_web/PHPMailer-master/src/SMTP.php';
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

    public function afficherUtilisateurs($tri = null) {
        $sql = "SELECT * FROM usser";
        
        // Ajouter le tri si spécifié
        if ($tri && in_array(strtoupper($tri), ['ASC', 'DESC'])) {
            $sql .= " ORDER BY nom " . strtoupper($tri);
        }
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
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
    public function getUtilisateurById($id) {
        // Vérifiez et corrigez le nom de la table ici
        $sql = "SELECT * FROM usser WHERE id = :id"; // ou le nom exact de votre table
        $db = config::getConnexion();
        
        try {
            $req = $db->prepare($sql);
            $req->bindValue(':id', $id);
            $req->execute();
            return $req->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Gestion d'erreur améliorée
            die("Erreur de base de données : " . $e->getMessage());
        }
    }
    public function verifyEmailAndGenerateCode($email)
    {
        $pdo = config::getConnexion();

        // Check if email exists
        $stmt = $pdo->prepare("SELECT * FROM usser WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate 5-character code
            $resetCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789'), 0, 5);

            // Update code in database
            $update = $pdo->prepare("UPDATE usser SET code = :code WHERE email = :email");
            $update->execute([
                'code' => $resetCode,
                'email' => $email
            ]);

            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'anoire.douiri@esprit.tn'; // ✅ Your Gmail
                $mail->Password = 'omoy foms wggm jlze';   // ✅ App Password (not Gmail password)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('anoire.douiri@esprit.tn ', 'Support');
                $mail->addAddress($email, $user['nom'] ?? '');

                $mail->isHTML(true);
                $mail->Subject = 'Code de réinitialisation';
                $mail->Body    = "Bonjour,<br><br>Votre code de réinitialisation est : <b>$resetCode</b><br><br>Merci.";

                $mail->send();
                return true;
            } catch (Exception $e) {
                error_log("Erreur lors de l'envoi du mail : {$mail->ErrorInfo}");
                return false;
            }
        } else {
            echo "<script>alert('Email non trouvé.');</script>";
            return false;
        }
    }
    /*public function trierUtilisateursParNom($ordre = 'ASC') {
        $sql = "SELECT * FROM usser ORDER BY nom $ordre";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }*/
}

    


?>
