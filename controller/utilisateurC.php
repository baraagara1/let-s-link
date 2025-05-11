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
                'mot' => $u->getMotpasse(), // ⚠ À crypter en production
                'role' => $u->getRole()
            ]);
        } catch (PDOException $e) {
            die('Erreur lors de l\'ajout : ' . $e->getMessage());
        }
    }

    public function afficherUtilisateurs($tri = null)
    {
        $sql = "SELECT * FROM usser";
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
                'mot' => $u->getMotpasse(), // ⚠ À crypter en production
                'role' => $u->getRole(),
                'id' => $id
            ]);
        } catch (PDOException $e) {
            die('Erreur lors de la modification : ' . $e->getMessage());
        }
    }

    public function supprimerUtilisateur($id)
    {
        $db = config::getConnexion();
        try {
            $check_sql = "SELECT id FROM usser WHERE id = :id";
            $check_query = $db->prepare($check_sql);
            $check_query->bindValue(':id', $id);
            $check_query->execute();

            if ($check_query->rowCount() == 0) {
                throw new Exception("L'utilisateur n'existe pas");
            }

            $delete_sql = "DELETE FROM usser WHERE id = :id";
            $delete_query = $db->prepare($delete_sql);
            $delete_query->bindValue(':id', $id);
            $delete_query->execute();
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

            if ($user && $motdepasse === $user['mot']) { // ⚠ À sécuriser avec password_verify() si hash
                return ['role' => $user['role'], 'id' => $user['id']];
            }

            return false;
        } catch (PDOException $e) {
            die('Erreur lors de la vérification de connexion : ' . $e->getMessage());
        }
    }

    public function getUtilisateurById($id)
    {
        $sql = "SELECT * FROM usser WHERE id = :id";
        $db = config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->bindValue(':id', $id);
            $req->execute();
            return $req->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erreur de base de données : " . $e->getMessage());
        }
    }

  /*  public function generateResetCodeForEmail($email)
{
    $pdo = config::getConnexion();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM usser WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        $resetCode = '';
        for ($i = 0; $i < 5; $i++) {
            $resetCode .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $update = $pdo->prepare("UPDATE usser SET code = :code WHERE email = :email");
        $update->execute(['code' => $resetCode, 'email' => $email]);

        echo "Rows updated: " . $update->rowCount(); // Debug line
        return $resetCode;
    } else {
        echo "<script>alert('Email not found: $email');</script>";
        return false;
    }
}*/


public function verifyEmailAndGenerateCode($email)
{
    $pdo = config::getConnexion();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM usser WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate new 5-character code
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        $resetCode = '';
        for ($i = 0; $i < 5; $i++) {
            $resetCode .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // Update code in database
        $update = $pdo->prepare("UPDATE usser SET code = :code WHERE email = :email");
        $update->execute(['code' => $resetCode, 'email' => $email]);

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'skylinemall10@gmail.com';
            $mail->Password = 'cmhi bxew hcbv cshq'; // Application-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('letslink@gmail.com', 'Support Lets link');
            $mail->addAddress($email, $user['nom'] ?? '');

            $mail->isHTML(true);
            $mail->Subject = 'Code de réinitialisation';
            $mail->Body = "Bonjour,<br><br>Votre code de réinitialisation est : <b>$resetCode</b><br><br>Merci.";

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


    
    public function getStatsForCircles() {
        $db = config::getConnexion();
        try {
            // Compter simplement le nombre total d'utilisateurs (supprime la partie actif/inactif)
            $query = $db->prepare("SELECT COUNT(*) as total FROM usser");
            $query->execute();
            $total = $query->fetchColumn();
    
            // Récupérer la répartition par rôle
            $query = $db->prepare("SELECT role, COUNT(*) as count FROM usser GROUP BY role");
            $query->execute();
            $roleStats = $query->fetchAll(PDO::FETCH_ASSOC);
    
            return [
                'roles' => $roleStats,
                'total' => $total
            ];
        } catch (PDOException $e) {
            die('Erreur lors de la récupération des statistiques : ' . $e->getMessage());
        }
    }
    public function verifyResetCode($email, $enteredCode) {
        $db = config::getConnexion();
        
        try {
            $stmt = $db->prepare("SELECT code FROM usser WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            
            // Debug: Afficher les valeurs pour vérification
            error_log("Code en base: " . $user['code']);
            error_log("Code entré: " . $enteredCode);
            
            if ($user && trim($user['code']) === trim($enteredCode)) {
                // Code valide, on supprime le code
                $stmt = $db->prepare("UPDATE usser SET code = NULL WHERE email = :email");
                $stmt->execute(['email' => $email]);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur de vérification du code: " . $e->getMessage());
            return false;
        }
    }


   

}





?>
