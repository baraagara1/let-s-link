<?php
require_once '../../../controller/ActiviteController.php';

// Vérifier si l'ID de l'activité est passé dans l'URL
if (isset($_GET['id'])) {
    $id_a = $_GET['id']; // Récupérer l'ID de l'activité à supprimer

    // Instancier le contrôleur et appeler la méthode de suppression
    $controller = new ActiviteController();

    // Supprimer l'activité
    $result = $controller->deleteActivite($id_a);

    // Vérifier si la suppression a réussi
    if ($result === true) {
        // Rediriger vers la page de gestion des activités après suppression
        header("Location: activites.php");
        exit();
    } else {
        // Afficher l'erreur si la suppression échoue
        echo "Erreur lors de la suppression de l'activité : " . $result;
    }
} else {
    echo "Aucun ID d'activité spécifié.";
}
?>
