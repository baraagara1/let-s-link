<?php
session_start();
require_once '../../../controller/FavorisController.php';

$id = $_SESSION['id'] ?? null;
$id_e = $_POST['id_e'] ?? null;

if ($id && $id_e) {
    $favorisController = new FavorisController();

    if ($favorisController->verifier($id, $id_e)) {
        $favorisController->supprimer($id, $id_e);
    } else {
        $favorisController->ajouter($id, $id_e);
    }

    header("Location: project.php"); // ou ajouter id_a et page dans l’URL si nécessaire
    exit;
} else {
    echo "Erreur : identifiants manquants.";
}
