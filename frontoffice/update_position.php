<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_cov'], $_POST['latitude'], $_POST['longitude'])) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $id_cov = intval($_POST['id_cov']);
            $latitude = floatval($_POST['latitude']);
            $longitude = floatval($_POST['longitude']);

            $stmt = $pdo->prepare("UPDATE covoiturage SET latitude = ?, longitude = ? WHERE id_cov = ?");
            $stmt->execute([$latitude, $longitude, $id_cov]);

            echo "✅ Position mise à jour.";
        } catch (PDOException $e) {
            echo "❌ Erreur BDD : " . $e->getMessage();
        }
    } else {
        echo "❌ Données manquantes.";
    }
} else {
    echo "❌ Mauvaise méthode HTTP.";
}
