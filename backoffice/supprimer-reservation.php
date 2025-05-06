<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id_res = ?");
        $stmt->execute([$id]);

        header("Location: reservation-list.php?supp=1");
        exit;

    } catch (PDOException $e) {
        die("Erreur de suppression : " . $e->getMessage());
    }
} else {
    header("Location: reservation-list.php");
    exit;
}
