<?php
require_once '../../../controller/EventController.php';
require_once '../../../controller/ActiviteController.php';

$successMessage = '';
$errorMessage = '';

// Récupérer l'id de l'activité depuis l'URL
$id_a = isset($_GET['id_a']) ? intval($_GET['id_a']) : null;

$activiteController = new ActiviteController();
$activites = !$id_a ? $activiteController->listActivites() : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_e = $_POST['nom_e'];
    $id_a = isset($_POST['id_a']) ? intval($_POST['id_a']) : null;
    $date_e = $_POST['date_e'];
    $lieu_e = $_POST['lieu_e'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../../uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = $fileName;
        } else {
            $errorMessage = "Erreur lors du téléchargement de l'image.";
            $image = null;
        }
    } else {
        $image = 'default.jpg'; // Par défaut
    }

    $controller = new EventController();
    $event = new Event($nom_e, $id_a, $date_e, $lieu_e, $image);
    $result = $controller->addEvent($event);

    $successMessage = $result === true ? "Événement ajouté avec succès !" : $result;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un événement</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>
<body id="page-top">

<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <img src="img/logo.png" alt="logo" width="150" height="60">
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link" href="Evénements.php">
                <i class="fas fa-fw fa-calendar"></i>
                <span>Retour aux événements</span>
            </a>
        </li>
        <hr class="sidebar-divider">
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <span class="nav-link">Ajout d'événement</span>
                    </li>
                </ul>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid">
                <h1 class="h3 mb-2 text-gray-800">Ajouter un événement</h1>

                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                <?php elseif ($errorMessage): ?>
                    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                <?php endif; ?>

                <form action="ajouter_evenement.php<?= $id_a ? '?id_a=' . $id_a : '' ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control form-control-user" name="nom_e" placeholder="Nom de l'événement" >
                        </div>
                        <div class="col-sm-6">
                            <?php if ($id_a): ?>
                                <input type="hidden" name="id_a" value="<?= htmlspecialchars($id_a) ?>">
                            <?php else: ?>
                                <select name="id_a" id="select-activite" class="form-control" >
    <option value="">-- Sélectionner une activité --</option>
    <?php foreach ($activites as $activite): ?>
        <option value="<?= $activite['id_a'] ?>">
            <?= htmlspecialchars($activite['nom_a']) ?>
        </option>
    <?php endforeach; ?>
</select>

                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <input type="date" class="form-control form-control-user" name="date_e" >
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control form-control-user" name="lieu_e" placeholder="Lieu" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Image :</label>
                        <input type="file" class="form-control form-control-user" name="image" accept="image/*" >
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-success btn-user btn-block">Ajouter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="text-center my-auto">
                    <span>&copy; Your Website 2025</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<script>
function validateForm(event) {
    const nom = document.querySelector('input[name="nom_e"]').value.trim();
    const dateStr = document.querySelector('input[name="date_e"]').value.trim();
    const lieu = document.querySelector('input[name="lieu_e"]').value.trim();
    const id_a = document.querySelector('select[name="id_a"]')?.value ?? "ok"; // "ok" pour ceux avec hidden
    const image = document.querySelector('input[name="image"]').files[0];

    if (!nom || !dateStr || !lieu || !id_a || !image) {
        alert("Veuillez remplir tous les champs.");
        event.preventDefault();
        return false;
    }

    if (/\d/.test(nom)) {
        alert("Le nom de l'événement ne doit pas contenir de chiffres.");
        event.preventDefault();
        return false;
    }

    const date = new Date(dateStr);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    if (isNaN(date.getTime()) || date <= today) {
        alert("La date de l'événement doit être une date valide dans le futur (format: aaaa-mm-jj).");
        event.preventDefault();
        return false;
    }

    if (!image.type.startsWith("image/")) {
        alert("Le fichier sélectionné doit être une image.");
        event.preventDefault();
        return false;
    }

    return true;
}
</script>

</body>
</html>
