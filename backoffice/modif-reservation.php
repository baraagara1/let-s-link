<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifie que la requête est bien un POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_res'];
    $utilisateur_id = trim($_POST['utilisateur_id']);
    $nb_place_res = $_POST['nb_place_res'];
    $moyen_paiement = $_POST['moyen_paiement'];
    $status = $_POST['status'];

    $errors = [];

    // 🔎 Validation
    if (!preg_match("/^\d{6}$/", $utilisateur_id)) {
        $errors[] = "L'ID utilisateur doit contenir exactement 6 chiffres.";
    }

    if (!is_numeric($nb_place_res) || $nb_place_res < 1) {
        $errors[] = "Le nombre de places doit être un nombre positif.";
    }

    if (empty($errors)) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 🔁 Exécuter la mise à jour
            $stmt = $pdo->prepare("
                UPDATE reservations 
                SET utilisateur_id = ?, nb_place_res = ?, moyen_paiement = ?, status = ? 
                WHERE id_res = ?
            ");
            $stmt->execute([
                $utilisateur_id,
                $nb_place_res,
                $moyen_paiement,
                $status,
                $id
            ]);

            header("Location: reservation-list.php?modif=1");
            exit;
        } catch (PDOException $e) {
            die("❌ Erreur de base de données : " . $e->getMessage());
        }
    } else {
        // Retourne avec erreurs
        header("Location: reservation-list.php?edit_res=$id&erreurs=1");
        exit;
    }
} else {
    // Accès direct interdit
    header("Location: reservation-list.php");
    exit;
}
?>
