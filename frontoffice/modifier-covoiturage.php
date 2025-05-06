<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Vérification de session
if (!isset($_SESSION['utilisateur_id'])) {
    http_response_code(403);
    exit("❌ Accès refusé. Veuillez vous connecter.");
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = intval($_POST['id_cov']);
    $destination = trim($_POST['destination']);
    $date = $_POST['date'];
    $places = $_POST['place_dispo'];
    $prix_c = $_POST['prix_c'];
    $heure_depart = $_POST['heure_depart'];
    $id_utilisateur = $_SESSION['utilisateur_id']; // 🔐 sécurité : on ne fait pas confiance à $_POST

    $errors = [];

    if (!is_numeric($prix_c) || $prix_c < 0) {
        $errors['prix_c'] = "Le prix doit être un nombre positif.";
    }

    if (!preg_match("/^[a-zA-Z\s]+$/", $destination)) {
        $errors['destination'] = "La destination ne doit contenir que des lettres et des espaces.";
    }

    $today = date('Y-m-d');
    if ($date < $today) {
        $errors['date'] = "La date doit être aujourd'hui ou dans le futur.";
    }

    if (!is_numeric($places) || $places < 1 || $places > 4) {
        $errors['place_dispo'] = "Le nombre de places doit être entre 1 et 4.";
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: lister-covoiturages.php?edit_cov=$id#form");
        exit;
    }

    // Vérifie que le covoiturage appartient à l'utilisateur connecté
    $check = $pdo->prepare("SELECT COUNT(*) FROM covoiturage WHERE id_cov = ? AND id_utilisateur = ?");
    $check->execute([$id, $id_utilisateur]);
    if ($check->fetchColumn() == 0) {
        exit("❌ Ce covoiturage ne vous appartient pas.");
    }

    $stmt = $pdo->prepare("UPDATE covoiturage 
        SET destination = ?, date = ?, place_dispo = ?, prix_c = ?, heure_depart = ? 
        WHERE id_cov = ?");
    $stmt->execute([$destination, $date, $places, $prix_c, $heure_depart, $id]);

    header("Location: lister-covoiturages.php?modif=1");
    exit;

} catch (PDOException $e) {
    die("❌ Erreur PDO : " . $e->getMessage());
}
