<?php
require_once '../../../controller/ActiviteController.php';

// Récupérer toutes les activités
$controller = new ActiviteController();
$activites = $controller->listActivites();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Activités</title>
    <link rel="icon" type="image/png" href="img/logo.png">

    <!-- Fonts et styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .bg-gradient-primary {
            background-color: rgb(45, 91, 216) !important;
            background-image: none !important;
        }

        .event-image {
            width: 100px;
            height: auto;
        }
    </style>
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
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
               aria-expanded="true" aria-controls="collapseTwo">
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

            <!-- Page Content -->
            <div class="container-fluid">
                <h1 class="h3 mb-2 text-gray-800">Table des activités</h1>

                <!-- Recherche -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par ID...">
                </div>

                <!-- Tableau -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-orange">Activités</h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Nom activité</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($activites as $activite): ?>
                                    <tr data-id="<?php echo $activite['id_a']; ?>">
                                        <td><?php echo htmlspecialchars($activite['nom_a']); ?></td>
                                        <td><?php echo htmlspecialchars($activite['description']); ?></td>
                                        <td>
                                            <?php
                                            if ($activite['image'] !== null) {
                                                echo '<img src="../../uploads/' . $activite['image'] . '" class="event-image" alt="Image de l\'activité">';
                                            } else {
                                                echo 'Pas d\'image';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="modifier_activite.php?id=<?php echo $activite['id_a']; ?>" class="btn btn-warning">Modifier</a>
                                            <a href="supprimer_activite.php?id=<?php echo $activite['id_a']; ?>" class="btn btn-danger btn-sm">Supprimer</a>
                                            <a href="Evénements.php?id_a=<?= $activite['id_a'] ?>" class="btn btn-primary btn-sm">évenement</a>
                       
                    </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <a href="ajouter_activite.php" class="btn btn-success">Ajouter une activité</a>
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

<!-- Recherche dynamique -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        var input = this.value.toLowerCase();
        var rows = document.querySelectorAll('#dataTable tbody tr');

        rows.forEach(function (row) {
            var id = row.getAttribute('data-id');
            row.style.display = id.toString().includes(input) ? '' : 'none';
        });
    });
</script>

</body>
</html>
