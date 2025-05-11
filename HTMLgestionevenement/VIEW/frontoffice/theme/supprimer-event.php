<?php
require_once '../../../controller/EventController.php';

// Vérifier si l'ID de l'événement est passé dans l'URL
if (isset($_GET['id'])) {
    $id_e = $_GET['id']; // Récupérer l'ID de l'événement à supprimer

    // Instancier le contrôleur et appeler la méthode de suppression
    $controller = new EventController();
    
    // Supprimer l'événement
    $result = $controller->deleteEvent($id_e);

    // Vérifier si la suppression a réussi
    if ($result === true) {
        // Rediriger vers la page de gestion des événements après suppression
        header("Location: project.php");
        exit();
    } else {
        // Afficher l'erreur si la suppression échoue
        echo "Erreur lors de la suppression de l'événement : " . $result;
    }
} else {
    echo "Aucun ID d'événement spécifié.";
}
?>
