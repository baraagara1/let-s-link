<?php
$pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("SELECT COUNT(*) FROM demandes_suppression WHERE statut = 'En attente'");
$count = $stmt->fetchColumn();

echo json_encode(['nouvellesDemandes' => $count > 0]);
?>
