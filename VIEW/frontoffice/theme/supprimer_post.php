<?php
require_once '../../../controller/PostController.php';

// Vérifier si l'ID du post est fourni dans l'URL
if (isset($_GET['id'])) {
    $id_p = $_GET['id'];  // Récupérer l'ID du post depuis l'URL

    // Créer une instance du contrôleur PostController pour appeler la fonction de suppression
    $controller = new PostController();
    if ($controller->deletePost($id_p)) {
        // Rediriger vers la page de gestion des blogs après la suppression
        header("Location: commentaire.php");
        exit();
    } else {
        echo "Erreur lors de la suppression du post.";
    }
} else {
    echo "ID du post non fourni.";
}
?>
