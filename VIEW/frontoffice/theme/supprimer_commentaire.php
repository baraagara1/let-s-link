<?php
require_once '../../../controller/CommentaireController.php';

if (isset($_GET['id'])) {
    $controller = new CommentaireController();
    $id_c = $_GET['id'];
    
    // Supprimer le commentaire
    if ($controller->deleteCommentaire($id_c)) {
        header("Location: commentaire.php");
    } else {
        echo "Erreur lors de la suppression du commentaire.";
    }
}
?>
