<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cov'])) {
    $id_cov = intval($_POST['id_cov']);

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Mettre le champ partage_actif à 1 pour ce covoiturage
        $stmt = $pdo->prepare("UPDATE covoiturage SET partage_actif = 1 WHERE id_cov = ?");
        $stmt->execute([$id_cov]);

        // Rediriger vers la liste après activation
        header("Location: lister-covoiturages.php");
        exit;

    } catch (PDOException $e) {
        die("❌ Erreur : " . $e->getMessage());
    }
} else {
    header("Location: lister-covoiturages.php");
    exit;
}
