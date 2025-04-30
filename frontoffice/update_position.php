<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=covoiturage_db;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_SESSION['id_utilisateur'], $_POST['id_cov'], $_POST['latitude'], $_POST['longitude'])) {
    $idCov = intval($_POST['id_cov']);
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    $idUtilisateur = $_SESSION['id_utilisateur'];

    // Vérifier si le covoiturage appartient bien à cet utilisateur
    $stmt = $pdo->prepare("SELECT id_utilisateur FROM covoiturage WHERE id_cov = ?");
    $stmt->execute([$idCov]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['id_utilisateur'] == $idUtilisateur) {
        // Mettre à jour latitude et longitude
        $update = $pdo->prepare("UPDATE covoiturage SET latitude = ?, longitude = ? WHERE id_cov = ?");
        $update->execute([$latitude, $longitude, $idCov]);
        echo "✅ Position mise à jour";
    } else {
        echo "⛔ Non autorisé";
    }
} else {
    echo "❌ Données manquantes";
}
?>
