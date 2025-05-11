<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header("Location: /mon_project_web/View/frontoffice/PROJECTS/login.php");
    exit();
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /mon_project_web/View/frontoffice/PROJECTS/profile.php"); 
    exit();
}

$userEmail = $_SESSION['user_email'];
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Pure Vibe - Admin</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .bg-gradient-primary { background-color: rgb(45, 91, 216) !important; }
        .dynamic-frame { border-left: 4px solid #e74a3b; transition: all 0.3s; }
        .profile-card { border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .profile-card:hover { transform: translateY(-5px); box-shadow: 0 6px 12px rgba(0,0,0,0.15); }
        .profile-header { background-color: #2d5bd8; color: white; border-top-left-radius: 10px; border-top-right-radius: 10px; }
    </style>
</head>
<body id="page-top">
<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <img src="img/logo.png" alt="logo" width="150" height="60">
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item active">
            <a class="nav-link" href="/mon_project_web/view/backoffice/backoffice/index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Tableau De Bord</span></a>
        </li>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Interface</div>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#usersMenu">
                <i class="fas fa-fw fa-users"></i>
                <span>Gestion des Utilisateurs</span>
            </a>
            <div id="usersMenu" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="ajouter.php"><i class="fas fa-plus-circle mr-2"></i>Ajouter</a>
                    <a class="collapse-item" href="modifier.php"><i class="fas fa-edit mr-2"></i>Modifier</a>
                    <a class="collapse-item" href="supprimer.php"><i class="fas fa-trash-alt mr-2"></i>Supprimer</a>
                    <a class="collapse-item" href="afficher.php"><i class="fas fa-eye mr-2"></i>Affichage</a>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-danger text-white" href="#" data-toggle="modal" data-target="#logoutModal" style="margin-top: 8px; margin-right: 10px;">
                            <i class="fas fa-sign-out-alt mr-1"></i> Déconnexion
                        </a>
                    </li>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($userEmail); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profil
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Déconnexion
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Tableau De Bord</h1>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-5 mb-4">
                        <div class="card profile-card">
                            <div class="profile-header py-3 text-center">
                                <h4 class="text-white"><?php echo htmlspecialchars($userEmail); ?></h4>
                                <span class="badge badge-success"><?php echo htmlspecialchars(ucfirst($userRole)); ?></span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="text-primary">Informations du compte</h6>
                                    <hr class="mt-1 mb-3">
                                    <div class="mb-2">
                                        <strong>Email:</strong>
                                        <span class="float-right"><?php echo htmlspecialchars($userEmail); ?></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Rôle:</strong>
                                        <span class="float-right"><?php echo htmlspecialchars(ucfirst($userRole)); ?></span>
                                    </div>
                                </div>
                                <a href="profile.php" class="btn btn-primary btn-block">
                                    <i class="fas fa-user-edit mr-2"></i> Modifier le profil
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8 col-lg-7">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Utilisateurs actifs</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Demandes en attente</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-comments fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4 dynamic-frame">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Bienvenue sur le Tableau de Bord</h6>
                            </div>
                            <div class="card-body">
                                <p>Bonjour <strong><?php echo htmlspecialchars($userEmail); ?></strong>, bienvenue dans votre espace d'administration.</p>
                                <p class="mb-0">Vous pouvez gérer les utilisateurs et consulter les statistiques depuis ce tableau de bord.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Pure Vibe <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Prêt à partir ?</h5>
                <button class="close" type="button" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">Cliquez sur "Déconnexion" ci-dessous pour terminer votre session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                <a class="btn btn-primary" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>
