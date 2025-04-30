<?php
session_start(); // Pour stocker les messages d'erreur

$host = 'localhost';
$dbname = 'covoiturage_db';
$user = 'root';
$password = '';

$errors = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination = trim($_POST['destination'] ?? '');
    $date = $_POST['date'] ?? '';
    $place_dispo = $_POST['place_dispo'] ?? '';
    $id_utilisateur = $_POST['id_utilisateur'] ?? '';

    // Contrôle destination (lettres et espaces uniquement)
    if (!preg_match('/^[a-zA-Z\s]+$/', $destination)) {
        $errors[] = "Destination invalide. Lettres et espaces uniquement.";
    }

    // Contrôle date : >= aujourd’hui
    if (empty($date) || strtotime($date) < strtotime(date('Y-m-d'))) {
        $errors[] = "Date invalide ou antérieure à aujourd'hui.";
    }

    // Contrôle places : > 0 et < 5
    if (!is_numeric($place_dispo) || $place_dispo < 1 || $place_dispo > 4) {
        $errors[] = "Nombre de places doit être entre 1 et 4.";
    }

    // Contrôle ID utilisateur : exactement 6 chiffres
    if (!preg_match('/^\d{6}$/', $id_utilisateur)) {
        $errors[] = "ID utilisateur doit contenir exactement 6 chiffres.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO covoiturage (destination, date, place_dispo, id_utilisateur) VALUES (:destination, :date, :place_dispo, :id_utilisateur)");
            $stmt->execute([
                ':destination' => $destination,
                ':date' => $date,
                ':place_dispo' => $place_dispo,
                ':id_utilisateur' => $id_utilisateur
            ]);

            $_SESSION['success'] = "Ajout effectué avec succès.";
            header("Location: covoituragelist.php");
            exit();

        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }

    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_values'] = $_POST;
    header("Location: covoiturage.php");
    exit();
}
?>
