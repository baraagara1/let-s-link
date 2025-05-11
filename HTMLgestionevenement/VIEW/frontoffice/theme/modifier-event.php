<?php
require_once '../../../controller/EventController.php';

$controller = new EventController();
$successMessage = '';
$errorMessage = '';
$event = null;

if (isset($_GET['id'])) {
    $event = $controller->getEventById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_e = $_POST['event-id'];
    $nom_e = $_POST['event-name'];
    $date_e = $_POST['event-date'];
    $lieu_e = $_POST['event-location'];

    // Upload image
    if (isset($_FILES['event-image']) && $_FILES['event-image']['error'] == 0) {
        $uploadDir = '../../uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = basename($_FILES['event-image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['event-image']['tmp_name'], $uploadFile)) {
            $image = $fileName;
        } else {
            $errorMessage = "Erreur lors de l'upload de l'image.";
            $image = null;
        }
    } else {
        $image = $event['image'];
    }

    if (!$errorMessage) {
        $id_a = intval($event['id_a']); // on garde la clé étrangère d’origine
        $updatedEvent = new Event($nom_e, $id_a, $date_e, $lieu_e, $image, $id_e);

        try {
            $controller->updateEvent($updatedEvent, $id_e);
            $successMessage = "L'événement a été modifié avec succès !";
            header("Refresh: 2; URL=project.php");
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Événement</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .form-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 50px;
        }
        .btn-primary {
            background-color: #06BBCC;
            border: none;
        }
        .btn-primary:hover {
            background-color: #05a3b3;
        }
    </style>

    <script>
    function validateForm(event) {
        const nom = document.getElementById('event-name').value.trim();
        const date = document.getElementById('event-date').value;
        const lieu = document.getElementById('event-location').value.trim();

        if (!nom || !date || !lieu) {
            alert("Veuillez remplir tous les champs.");
            event.preventDefault();
            return false;
        }

        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0,0,0,0);

        if (selectedDate < today) {
            alert("La date doit être dans le futur.");
            event.preventDefault();
            return false;
        }

        return true;
    }
    </script>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-container">
                <h2 class="mb-4 text-center">Modifier un Événement</h2>

                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?= $successMessage ?></div>
                <?php elseif ($errorMessage): ?>
                    <div class="alert alert-danger"><?= $errorMessage ?></div>
                <?php endif; ?>

                <?php if ($event): ?>
                <form action="modifier-event.php?id=<?= $event['id_e'] ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
                    <input type="hidden" name="event-id" value="<?= $event['id_e'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Nom de l'Événement</label>
                        <input type="text" class="form-control" id="event-name" name="event-name" value="<?= htmlspecialchars($event['nom_e']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="event-date" name="event-date" value="<?= $event['date_e'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lieu</label>
                        <input type="text" class="form-control" id="event-location" name="event-location" value="<?= htmlspecialchars($event['lieu_e']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" id="event-image" name="event-image" accept="image/*">
                        <div class="form-text">Image actuelle : <?= $event['image'] ?></div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5 py-2">Modifier</button>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-warning">Aucun événement trouvé.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
