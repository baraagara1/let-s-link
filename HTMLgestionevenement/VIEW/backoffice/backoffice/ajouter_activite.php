<?php
require_once '../../../controller/ActiviteController.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_a = $_POST['nom_a'];
    $description = $_POST['description'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = $fileName;
        } else {
            $errorMessage = "Erreur lors du téléchargement de l'image.";
            $image = null;
        }
    } else {
        $image = null;
    }

    $activite = new Activite($nom_a, $description, $image, 0); // id_u = 0
    $controller = new ActiviteController();
    $result = $controller->addActivite($activite);

    if ($result === true) {
        $successMessage = "Activité ajoutée avec succès !";
    } else {
        $errorMessage = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une activité</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
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
                    <span>Gestion des activités</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Composants personnalisés:</h6>
                        <a class="collapse-item" href="activites.php">Activités</a>
                    </div>
                </div>
            </li>
            <hr class="sidebar-divider">
        </ul>
        <!-- End of Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
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

                <!-- Contenu principal -->
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Ajouter une activité</h1>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?php echo $successMessage; ?></div>
                    <?php elseif ($errorMessage): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>

                    <form action="ajouter_activite.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-user" name="nom_a" placeholder="Nom de l'activité">
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-user" name="description" placeholder="Description de l'activité">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image">Image (obligatoire) :</label>
                            <input type="file" class="form-control form-control-user" name="image" accept="image/*">
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
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2025</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <script>
        function validateForm(event) {
            var nom = document.querySelector('input[name="nom_a"]').value;
            var description = document.querySelector('input[name="description"]').value;
            var image = document.querySelector('input[name="image"]').files[0];

            var nameRegex = /^[a-zA-ZÀ-ÿ\s]+$/;
            if (!nameRegex.test(nom)) {
                alert("Le nom de l'activité doit contenir uniquement des lettres.");
                event.preventDefault();
                return false;
            }

            if (!description) {
                alert("La description est obligatoire.");
                event.preventDefault();
                return false;
            }

            if (!image) {
                alert("L'image est obligatoire.");
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>

</body>
</html>
