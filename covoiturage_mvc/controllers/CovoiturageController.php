<?php
require_once 'models/Covoiturage.php';

class CovoiturageController {

    // Afficher la liste des covoiturages
    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $covoiturages = Covoiturage::getAll();
        include 'views/covoiturage/liste.php';
    }

   


    // Afficher le formulaire de modification
    public function modifier() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if (isset($_GET['id'])) {
            $cov = Covoiturage::getById($_GET['id']);
            include 'views/covoiturage/modifier.php';
        }
    

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Covoiturage::modifier($_POST);
            header('Location: index.php?action=index');
            exit;
        }
    }

    public function supprimer() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
    
        $idUtilisateurConnecte = $_SESSION['user_id'];
        $id_cov = $_GET['id'] ?? 0;
    
        $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // 🔒 Vérification propriétaire
        $check = $pdo->prepare("SELECT user_id FROM covoiturage WHERE id_cov = ?");
        $check->execute([$id_cov]);
        $owner = $check->fetchColumn();
    
        if ($owner != $idUtilisateurConnecte) {
            die("❌ Suppression refusée : ce trajet ne vous appartient pas.");
        }
    
        // Supprimer
        $stmt = $pdo->prepare("DELETE FROM covoiturage WHERE id_cov = ?");
        $stmt->execute([$id_cov]);
    
        header("Location: index.php?action=lister&deleted=1");
        exit;
    }
    

    public function mettreAJour() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
    
        $idUtilisateurConnecte = $_SESSION['user_id'];
        $id_cov = $_POST['id_cov'];
    
        $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // ✅ Vérifie que ce covoiturage appartient à l'utilisateur connecté
        $check = $pdo->prepare("SELECT user_id FROM covoiturage WHERE id_cov = ?");
        $check->execute([$id_cov]);
        $owner = $check->fetchColumn();
    
        if ($owner != $idUtilisateurConnecte) {
            die("❌ Action interdite : ce covoiturage ne vous appartient pas.");
        }
    
        // Ensuite, fais la mise à jour...
        $stmt = $pdo->prepare("UPDATE covoiturage SET lieu_depart = ?, destination = ?, date = ?, place_dispo = ?, prix_c = ?, user_id = ?, heure_depart = ? WHERE id_cov = ?");
        $stmt->execute([
            $_POST['lieu_depart'],
            $_POST['destination'],
            $_POST['date'],
            $_POST['place_dispo'],
            $_POST['prix_c'],
            $_POST['user_id'],
            $_POST['heure_depart'],
            $id_cov
        ]);
    
        header("Location: index.php?action=lister");
        exit;
    }

    public function ajouter()
{
    session_start();
    $errors = [];
    $data = [
        'destination' => '',
        'date' => '',
        'place_dispo' => '',
        'prix_c' => '',
        'heure_depart' => '',
        'lieu_depart' => ''
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = array_map('trim', $_POST);
        $errors = [
            'destination' => !preg_match("/^[a-zA-Z\s]+$/", $data['destination']) ? "Uniquement lettres et espaces." : '',
            'date' => ($data['date'] < date('Y-m-d')) ? "Date invalide." : '',
            'place_dispo' => (!is_numeric($data['place_dispo']) || $data['place_dispo'] < 1 || $data['place_dispo'] > 4) ? "Entre 1 et 4." : '',
            'prix_c' => (!is_numeric($data['prix_c']) || $data['prix_c'] < 0) ? "Prix invalide." : '',
            'heure_depart' => empty($data['heure_depart']) ? "Champ requis." : '',
'lieu_depart' => !preg_match("/^[a-zA-Z\s]+$/", $data['lieu_depart']) ? "Uniquement lettres et espaces." : ''
        ];

        if (!array_filter($errors)) {
            require_once 'models/Covoiturage.php';
            $data['user_id'] = $_SESSION['user_id'];

            Covoiturage::ajouter($data);
            
            header("Location: index.php?action=ajouter&success=1");
            exit;
        }
    }

    require 'views/covoiturage/ajouter.php';
}

    
    
    
}
