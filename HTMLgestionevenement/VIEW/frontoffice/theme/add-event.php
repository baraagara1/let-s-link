<?php
require_once '../../../controller/EventController.php';
session_start();

$successMessage = '';
$errorMessage = '';

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupère les données du formulaire
    $nom_e = $_POST['event-name'];
    $id_a = isset($_POST['event-id-a']) ? intval($_POST['event-id-a']) : null;  // ID de l'activité
    $date_e = $_POST['event-date'];
    $lieu_e = $_POST['event-location'];

    // Gestion de l'image téléchargée
    if (isset($_FILES['event-image']) && $_FILES['event-image']['error'] == 0) {
        $uploadDir = '../../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['event-image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['event-image']['tmp_name'], $uploadFile)) {
            $image = $fileName;
        } else {
            $errorMessage = "Erreur lors du téléchargement de l'image.";
            $image = null;
        }
    } else {
        $image = null;
    }

    // Si aucune image n'est téléchargée, on utilise l'image par défaut
    if ($image === null) {
        $image = 'default.jpg';
    }

    // Ajout de l'événement avec les informations récupérées
    $controller = new EventController();
    $event = new Event($nom_e, $id_a, $date_e, $lieu_e, $image, $_SESSION['id']);
    $result = $controller->addEvent($event);

    // Vérifie si l'événement a été ajouté avec succès
    if ($result === true) {
        $successMessage = "Événement ajouté avec succès!";
    } else {
        $errorMessage = $result;
    }
}
?>
