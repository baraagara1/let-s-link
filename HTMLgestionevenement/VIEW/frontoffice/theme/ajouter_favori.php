<?php
require_once '../../../config.php';
session_start();

$id_e = $_POST['id_e'] ?? null;
$id = $_POST['id'] ?? null;

if ($id_e && $id) {
    $db = config::getConnexion();
    $stmt = $db->prepare("INSERT IGNORE INTO favoris (id, id_e) VALUES (?, ?)");
    $stmt->execute([$id, $id_e]);

    $_SESSION['message'] = "Événement ajouté aux favoris.";
} else {
    $_SESSION['message'] = "Erreur lors de l'ajout aux favoris.";
}

header("Location: project.php");
exit;
?>
