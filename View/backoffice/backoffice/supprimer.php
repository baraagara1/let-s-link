<?php
require_once 'C:\xampp\htdocs\mon_project_web\config.php';
require_once 'C:\xampp\htdocs\mon_project_web\model\utilisateur.php';
require_once 'C:\xampp\htdocs\mon_project_web\controller\utilisateurC.php';

$utilisateurC = new UtilisateurC();
$error = '';
$success = '';

// Traitement de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    try {
        $userId = (int)$_POST['user_id'];
        $utilisateurC->supprimerUtilisateur($userId);
        $success = "L'utilisateur a été supprimé avec succès";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Récupération de la liste des utilisateurs
$utilisateurs = $utilisateurC->afficherUtilisateurs();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Pure Vibe - Supprimer Utilisateur</title>
    <link rel="icon" type="image/png" href="img/logo.png">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    
    <style>
        .bg-gradient-primary {
            background-color: rgb(45, 91, 216) !important;
            background-image: none !important;
        }
        .dynamic-frame {
            border-left: 4px solid #e74a3b;
            transition: all 0.3s;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .alert-warning {
            border-left: 4px solid #f6c23e;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <img src="img/logo.png" alt="logo" width="150" height="60">
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tableau De Bord</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Gestion des Utilisateurs -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#usersMenu" 
                   aria-expanded="true" aria-controls="usersMenu">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Gestion des Utilisateurs</span>
                </a>
                <div id="usersMenu" class="collapse show" aria-labelledby="usersMenu">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="ajouter.php">
                            <i class="fas fa-plus-circle mr-2"></i>Ajouter
                        </a>
                        <a class="collapse-item" href="modifier.php">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                        <a class="collapse-item active" href="supprimer.php">
                            <i class="fas fa-trash-alt mr-2"></i>Supprimer
                        </a>
                        <a class="collapse-item" href="afficher.php">
                            <i class="fas fa-eye mr-2"></i>Affichage
                        </a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Administrateur</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
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
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Supprimer un Utilisateur</h1>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4 dynamic-frame">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Liste des utilisateurs</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Attention :</strong> La suppression d'un utilisateur est irréversible. Cette action supprimera définitivement toutes les données associées à cet utilisateur.
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="deleteUsersTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Email</th>
                                                    <th>Rôle</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($utilisateurs as $user): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['prenom'] . ' ' . $user['nom'])) ?>')">
                                                            <i class="fas fa-trash-alt"></i> Supprimer
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Pure Vibe 2023</span>
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

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Prêt à partir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Sélectionnez "Déconnexion" ci-dessous si vous êtes prêt à terminer votre session actuelle.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                    <a class="btn btn-primary" href="login.php">Déconnexion</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <form method="post" id="deleteForm">
                    <input type="hidden" name="user_id" id="userIdToDelete">
                    <input type="hidden" name="delete_user" value="1">
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userToDeleteName"></strong> ?</p>
                        <p class="text-danger"><i class="fas fa-exclamation-circle mr-2"></i>Cette action est irréversible et supprimera toutes les données associées à cet utilisateur.</p>
                        <div class="form-group">
                            <label for="confirmText">Tapez <strong>"CONFIRMER"</strong> pour valider :</label>
                            <input type="text" class="form-control" id="confirmText" placeholder="CONFIRMER" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                            <i class="fas fa-trash-alt mr-1"></i> Supprimer définitivement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script>
    // Confirmer la suppression
    function confirmDelete(userId, userName) {
        $('#userToDeleteName').text(userName);
        $('#userIdToDelete').val(userId);
        $('#deleteModal').modal('show');
        $('#confirmText').val('');
        $('#confirmDeleteBtn').prop('disabled', true);
    }

    // Initialisation
    $(document).ready(function() {
        // Activer le bouton de suppression seulement si "CONFIRMER" est tapé
        $('#confirmText').on('input', function() {
            if ($(this).val().toUpperCase() === 'CONFIRMER') {
                $('#confirmDeleteBtn').prop('disabled', false);
            } else {
                $('#confirmDeleteBtn').prop('disabled', true);
            }
        });

        // Empêcher la soumission du formulaire si la confirmation n'est pas valide
        $('#deleteForm').on('submit', function(e) {
            if ($('#confirmText').val().toUpperCase() !== 'CONFIRMER') {
                e.preventDefault();
                alert('Veuillez taper "CONFIRMER" pour valider la suppression');
            }
        });
    });
    </script>
</body>
</html>