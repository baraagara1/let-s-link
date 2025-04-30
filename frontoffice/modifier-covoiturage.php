<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['id_cov'];
    $destination = trim($_POST['destination']);
    $date = $_POST['date'];
    $places = $_POST['place_dispo'];
    $id_utilisateur = $_POST['id_utilisateur'];
    $prix_c = $_POST['prix_c'];

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

    if (!preg_match("/^\d{6}$/", $id_utilisateur)) {
        $errors['id_utilisateur'] = "L'ID utilisateur doit contenir exactement 6 chiffres.";
    }

    if (!empty($errors)) {
        session_start();
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: lister-covoiturages.php?edit_cov=$id#form");
        exit;
    }

    $stmt = $pdo->prepare("UPDATE covoiturage SET destination = ?, date = ?, place_dispo = ?, id_utilisateur = ?, prix_c = ? WHERE id_cov = ?");
    $stmt->execute([$destination, $date, $places, $id_utilisateur, $prix_c, $id]);

    header("Location: lister-covoiturages.php?modif=1");
    exit;

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
