<?php
session_start();
require_once '../../../config.php';

if (isset($_GET['id_e']) && isset($_GET['id'])) {
    $id_e = $_GET['id_e'];  // ID de l'événement
    $id = $_GET['id'];  // ID de l'utilisateur

    try {
        $db = config::getConnexion();

        // Vérifie si l'utilisateur est déjà inscrit à cet événement
        $stmt = $db->prepare("SELECT COUNT(*) FROM participants WHERE id = :id AND id_e = :id_e");
        $stmt->execute(['id' => $id, 'id_e' => $id_e]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Si l'utilisateur n'est pas encore inscrit, on l'ajoute
            $stmt = $db->prepare("INSERT INTO participants (id, id_e) VALUES (:id, :id_e)");
            $stmt->execute(['id' => $id, 'id_e' => $id_e]);

            $_SESSION['message'] = "Vous avez bien participé à l'événement.";
        } else {
            // Si l'utilisateur est déjà inscrit
            $_SESSION['message'] = "Vous êtes déjà inscrit à cet événement.";
        }

        // Redirige vers la page de l'événement ou des projets
        header("Location: project.php?id=" . $id);  // Redirige vers la page des événements
        exit;
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    $_SESSION['message'] = "Erreur, paramètres manquants.";
    header("Location: project.php?id=" . $_SESSION['id']);  // Redirige vers la page des événements
    exit;
}
?>
