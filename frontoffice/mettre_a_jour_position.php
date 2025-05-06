<?php
session_start();

// ✅ Vérifier que les bons paramètres sont reçus
if (!isset($_POST['id_cov'], $_POST['latitude'], $_POST['longitude'])) {
    http_response_code(400);
    exit("❌ Paramètres manquants.");
}

// ✅ Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    http_response_code(403);
    exit("❌ Session expirée.");
}

$id_cov = intval($_POST['id_cov']);
$lat = floatval($_POST['latitude']);
$lng = floatval($_POST['longitude']);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Vérifier que l'utilisateur connecté est bien le conducteur du covoiturage
    $stmt = $pdo->prepare("SELECT id_utilisateur FROM covoiturage WHERE id_cov = ?");
    $stmt->execute([$id_cov]);
    $conducteur = $stmt->fetchColumn();

    if ($conducteur != $_SESSION['utilisateur_id']) {
        http_response_code(403);
        exit("❌ Accès refusé.");
    }

    // ✅ Mettre à jour la position du conducteur
    $update = $pdo->prepare("UPDATE covoiturage SET latitude = ?, longitude = ? WHERE id_cov = ?");
    $update->execute([$lat, $lng, $id_cov]);

    echo "✅ Position mise à jour.";

} catch (PDOException $e) {
    http_response_code(500);
    exit("❌ Erreur BDD : " . $e->getMessage());
}
