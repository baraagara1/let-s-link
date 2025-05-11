<?php
require_once '../../../controller/EventController.php';

$controller = new EventController();
$successMessage = '';
$errorMessage = '';
$event = null;

// Récupération de l’événement
if (isset($_GET['id'])) {
    $event = $controller->getEventById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_e = $_POST['event-id'];
    $nom_e = $_POST['event-name'];
    $date_e = $_POST['event-date'];
    $lieu_e = $_POST['event-location'];

    // Upload image (facultatif)
    if (isset($_FILES['event-image']) && $_FILES['event-image']['error'] == 0) {
        $uploadDir = '../../uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = basename($_FILES['event-image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['event-image']['tmp_name'], $uploadFile)) {
            $image = $fileName;
        } else {
            $errorMessage = "Erreur lors du téléchargement de l'image.";
            $image = null;
        }
    } else {
        $image = $event['image']; // on garde l'image actuelle
    }

    if (!$errorMessage) {
        $id_a = intval($event['id_a']); // conserver la liaison activité
        $updatedEvent = new Event($nom_e, $id_a, $date_e, $lieu_e, $image, $id_e);

        try {
            $controller->updateEvent($updatedEvent, $id_e);
            $successMessage = "L'événement a été modifié avec succès !";
            header("Refresh: 2; URL=activites.php");
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un événement</title>
    <!-- Inclure les fichiers CSS nécessaires -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Script de validation JavaScript -->
    <script>
        // Fonction de validation du formulaire
        function validateForm(event) {
    const nom = document.querySelector('input[name="event-name"]').value.trim();
    const date = document.querySelector('input[name="event-date"]').value;
    const lieu = document.querySelector('input[name="event-location"]').value.trim();

    if (!nom || !date || !lieu) {
        alert("Veuillez remplir tous les champs.");
        event.preventDefault();
        return false;
    }

    const selectedDate = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
        alert("La date doit être dans le futur.");
        event.preventDefault();
        return false;
    }

    return true;
}

    </script>
</head>
<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <img src="img/logo.png" alt="logo" width="150" height="60">
            </a>
            <hr class="sidebar-divider my-0">
            <div class="sidebar-heading">Interface</div>
            <li class="nav-item">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tableau De Bord</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Gestion des événements</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Composants personnalisés:</h6>
                        <a class="collapse-item" href="Evénements.php">Evénements</a>
                    </div>
                </div>
            </li>
            <hr class="sidebar-divider">
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">utilisateur</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Page Content -->
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Modifier un événement</h1>

                    <!-- Afficher les messages de succès ou d'erreur -->
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?php echo $successMessage; ?></div>
                    <?php elseif ($errorMessage): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>

                    <!-- Formulaire pour modifier l'événement -->
                    <form action="modifier_evenement.php?id=<?php echo htmlspecialchars($event['id_e']); ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
                        <!-- Champ caché pour l'ID -->
                        <input type="hidden" name="event-id" value="<?= $event['id_e']; ?>">

<div class="form-group row">
    <div class="col-sm-6 mb-3 mb-sm-0">
        <input type="text" class="form-control form-control-user" name="event-name" value="<?= htmlspecialchars($event['nom_e']); ?>">
    </div>
</div>

<div class="form-group">
    <input type="text" class="form-control form-control-user" name="event-location" value="<?= htmlspecialchars($event['lieu_e']); ?>">
</div>

<div class="form-group row">
    <div class="col-sm-6 mb-3 mb-sm-0">
        <input type="date" class="form-control form-control-user" name="event-date" value="<?= $event['date_e']; ?>">
    </div>
</div>

<div class="form-group">
    <label for="event-image">Image (facultatif) :</label>
    <input type="file" class="form-control form-control-user" name="event-image" accept="image/*,video/*">
</div>

                        <div class="form-group row">
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-success btn-user btn-block">Modifier l'événement</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>
</html>
