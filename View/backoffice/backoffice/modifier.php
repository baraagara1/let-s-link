<?php
require_once 'C:\xampp\htdocs\mon_project_web\config.php';
require_once 'C:\xampp\htdocs\mon_project_web\model\utilisateur.php';
require_once 'C:\xampp\htdocs\mon_project_web\controller\utilisateurC.php';

$utilisateurC = new UtilisateurC();
$error = '';
$success = '';

try {
    $utilisateurs = $utilisateurC->afficherUtilisateurs();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
        if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email']) || empty($_POST['telephone']) || empty($_POST['role'])) {
            throw new Exception("Tous les champs obligatoires doivent être remplis");
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("L'adresse email n'est pas valide");
        }

        if (!preg_match('/^[0-9]{10}$/', $_POST['telephone'])) {
            throw new Exception("Le numéro de téléphone doit contenir 10 chiffres");
        }

        $utilisateurExistant = array_filter($utilisateurs, fn($u) => $u['id'] == $_POST['id_utilisateur']);
        $utilisateurExistant = reset($utilisateurExistant);

        $motpasse = !empty($_POST['motpasse']) ? $_POST['motpasse'] : $utilisateurExistant['mot'];

        $utilisateur = new Utilisateur(
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['email'],
            $_POST['telephone'],
            '', // Adresse (supprimée dans la nouvelle version)
            $motpasse,
            $_POST['role'],
            isset($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : null
        );

        $utilisateurC->modifierUtilisateur((int)$_POST['id_utilisateur'], $utilisateur);
        $success = "Utilisateur modifié avec succès";
        // Recharger la liste après modification
        $utilisateurs = $utilisateurC->afficherUtilisateurs();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Pure Vibe - Modifier Utilisateur</title>
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
        .password-strength {
            height: 5px;
            margin-top: 5px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s;
        }
        .error-message {
            color: #e74a3b;
            font-size: 0.8rem;
            margin-top: 5px;
            display: none;
        }
        .is-invalid {
            border-color: #e74a3b !important;
        }
        #edit-form-container {
            display: none;
            margin-top: 20px;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <img src="img/logo.png" alt="logo" width="150" height="60">
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.html">
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
                        <a class="collapse-item active" href="modifier.php">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                        <a class="collapse-item" href="supprimer.php">
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
                        <h1 class="h3 mb-0 text-gray-800">Modifier un Utilisateur</h1>
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
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Email</th>
                                                    <th>Téléphone</th>
                                                    <th>Rôle</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($utilisateurs as $user): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                    <td><?= htmlspecialchars($user['telephone']) ?></td>
                                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" onclick="loadUserData(<?= $user['id'] ?>)">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Formulaire de modification (caché par défaut) -->
                                    <div id="edit-form-container" class="mt-4">
                                        <h5 class="mb-3"><i class="fas fa-user-edit mr-2"></i>Modifier l'utilisateur</h5>
                                        <form id="edit-user-form" method="post">
                                            <input type="hidden" id="edit-user-id" name="id_utilisateur">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Nom <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edit-last-name" name="nom" required>
                                                    <div class="error-message" id="edit-last-name-error">Le nom ne doit pas contenir de chiffres ou espaces</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label>Prénom <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edit-first-name" name="prenom" required>
                                                    <div class="error-message" id="edit-first-name-error">Le prénom ne doit pas contenir de chiffres ou espaces</div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="edit-email" name="email" required>
                                                    <div class="error-message" id="edit-email-error">Veuillez entrer une adresse email valide</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label>Téléphone <span class="text-danger">*</span></label>
                                                    <input type="tel" class="form-control" id="edit-phone" name="telephone" pattern="[0-9]{10}" required>
                                                    <div class="error-message" id="edit-phone-error">Veuillez entrer un numéro valide (10 chiffres)</div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Rôle <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="edit-role" name="role" required>
                                                        <option value="admin">Administrateur</option>
                                                        <option value="user">Utilisateur</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Mot de passe (laisser vide pour ne pas changer)</label>
                                                    <input type="password" class="form-control" id="edit-password" name="motpasse">
                                                    <div class="password-strength">
                                                        <div class="password-strength-bar" id="edit-password-strength-bar"></div>
                                                    </div>
                                                    <div class="error-message" id="edit-password-error">
                                                        Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label>Confirmer le mot de passe</label>
                                                    <input type="password" class="form-control" id="edit-confirm-password">
                                                    <div class="error-message" id="edit-confirm-password-error">Les mots de passe ne correspondent pas</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-secondary mr-2" onclick="cancelEdit()">
                                                    <i class="fas fa-times mr-1"></i> Annuler
                                                </button>
                                                <button type="submit" class="btn btn-primary" name="modifier">
                                                    <i class="fas fa-save mr-1"></i> Enregistrer
                                                </button>
                                            </div>
                                        </form>
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
                    <a class="btn btn-primary" href="login.html">Déconnexion</a>
                </div>
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
    // Charger les données de l'utilisateur à modifier
    function loadUserData(userId) {
        // Ici, vous devriez normalement faire une requête AJAX pour récupérer les données de l'utilisateur
        // Pour cet exemple, nous allons simuler les données
        const users = <?= json_encode($utilisateurs) ?>;
        const user = users.find(u => u.id == userId);
        
        if (user) {
            $('#edit-user-id').val(user.id);
            $('#edit-last-name').val(user.nom);
            $('#edit-first-name').val(user.prenom);
            $('#edit-email').val(user.email);
            $('#edit-phone').val(user.telephone);
            $('#edit-role').val(user.role);
            
            $('#usersTable_wrapper').hide();
            $('#edit-form-container').fadeIn();
            
            $('html, body').animate({
                scrollTop: $('#edit-form-container').offset().top - 20
            }, 500);
        }
    }

    // Annuler la modification
    function cancelEdit() {
        $('#edit-form-container').hide();
        $('#usersTable_wrapper').fadeIn();
        $('#edit-user-form')[0].reset();
        $('.error-message').hide();
        $('.form-control').removeClass('is-invalid');
    }

    // Validation du formulaire de modification
    function validateEditForm() {
        let isValid = true;

        // Validation du nom
        const lastName = $('#edit-last-name').val().trim();
        if (lastName === '') {
            $('#edit-last-name').addClass('is-invalid');
            $('#edit-last-name-error').text('Veuillez entrer un nom valide').show();
            isValid = false;
        } else if (/\d/.test(lastName) || /\s/.test(lastName)) {
            $('#edit-last-name').addClass('is-invalid');
            $('#edit-last-name-error').text('Le nom ne doit pas contenir de chiffres ou espaces').show();
            isValid = false;
        } else if (lastName[0] !== lastName[0].toUpperCase()) {
            $('#edit-last-name').addClass('is-invalid');
            $('#edit-last-name-error').text('La première lettre doit être en majuscule').show();
            isValid = false;
        } else {
            $('#edit-last-name').removeClass('is-invalid');
            $('#edit-last-name-error').hide();
        }

        // Validation du prénom
        const firstName = $('#edit-first-name').val().trim();
        if (firstName === '') {
            $('#edit-first-name').addClass('is-invalid');
            $('#edit-first-name-error').text('Veuillez entrer un prénom valide').show();
            isValid = false;
        } else if (/\d/.test(firstName) || /\s/.test(firstName)) {
            $('#edit-first-name').addClass('is-invalid');
            $('#edit-first-name-error').text('Le prénom ne doit pas contenir de chiffres ou espaces').show();
            isValid = false;
        } else if (firstName[0] !== firstName[0].toUpperCase()) {
            $('#edit-first-name').addClass('is-invalid');
            $('#edit-first-name-error').text('La première lettre doit être en majuscule').show();
            isValid = false;
        } else {
            $('#edit-first-name').removeClass('is-invalid');
            $('#edit-first-name-error').hide();
        }

        // Validation de l'email
        const email = $('#edit-email').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email === '' || !emailRegex.test(email)) {
            $('#edit-email').addClass('is-invalid');
            $('#edit-email-error').show();
            isValid = false;
        } else {
            $('#edit-email').removeClass('is-invalid');
            $('#edit-email-error').hide();
        }

        // Validation du téléphone
        const phone = $('#edit-phone').val().trim();
        const phoneRegex = /^[0-9]{10}$/;
        if (phone === '' || !phoneRegex.test(phone)) {
            $('#edit-phone').addClass('is-invalid');
            $('#edit-phone-error').show();
            isValid = false;
        } else {
            $('#edit-phone').removeClass('is-invalid');
            $('#edit-phone-error').hide();
        }

        // Validation du mot de passe (seulement si rempli)
        const password = $('#edit-password').val();
        if (password !== '') {
            const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{8,}$/;
            if (!passwordRegex.test(password)) {
                $('#edit-password').addClass('is-invalid');
                $('#edit-password-error').show();
                isValid = false;
            } else {
                $('#edit-password').removeClass('is-invalid');
                $('#edit-password-error').hide();
            }

            // Validation de la confirmation du mot de passe
            const confirmPassword = $('#edit-confirm-password').val();
            if (confirmPassword === '') {
                $('#edit-confirm-password').addClass('is-invalid');
                $('#edit-confirm-password-error').text('Veuillez confirmer le mot de passe').show();
                isValid = false;
            } else if (confirmPassword !== password) {
                $('#edit-confirm-password').addClass('is-invalid');
                $('#edit-confirm-password-error').text('Les mots de passe ne correspondent pas').show();
                isValid = false;
            } else {
                $('#edit-confirm-password').removeClass('is-invalid');
                $('#edit-confirm-password-error').hide();
            }
        }

        return isValid;
    }

    // Initialisation
    $(document).ready(function() {
        // Écouteur pour la force du mot de passe
        $('#edit-password').on('input', function() {
            const password = $(this).val();
            const strengthBar = $('#edit-password-strength-bar');
            let strength = 0;
            
            if (password.length > 0) strength += 10;
            if (password.length >= 8) strength += 20;
            if (password.match(/\d/)) strength += 20;
            if (password.match(/[a-z]/)) strength += 20;
            if (password.match(/[A-Z]/)) strength += 20;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 10;
            
            strengthBar.css('width', strength + '%');
            
            if (strength < 40) {
                strengthBar.css('background-color', '#e74a3b');
            } else if (strength < 80) {
                strengthBar.css('background-color', '#f6c23e');
            } else {
                strengthBar.css('background-color', '#1cc88a');
            }
        });

        // Validation lors de la soumission du formulaire
        $('#edit-user-form').on('submit', function(e) {
            if (!validateEditForm()) {
                e.preventDefault();
                
                // Faire défiler jusqu'au premier champ invalide
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 500);
                
                // Afficher des alertes si nécessaire
                const lastName = $('#edit-last-name').val().trim();
                const firstName = $('#edit-first-name').val().trim();
                const phone = $('#edit-phone').val().trim();
                
                if (/\d/.test(lastName) || /\s/.test(lastName)) {
                    alert("Le nom ne doit pas contenir de chiffres ou d'espaces !");
                    $('#edit-last-name').focus();
                    return false;
                }
                
                if (/\d/.test(firstName) || /\s/.test(firstName)) {
                    alert("Le prénom ne doit pas contenir de chiffres ou d'espaces !");
                    $('#edit-first-name').focus();
                    return false;
                }
                
                if (lastName[0] !== lastName[0].toUpperCase()) {
                    alert("La première lettre du nom doit être en majuscule !");
                    $('#edit-last-name').focus();
                    return false;
                }
                
                if (firstName[0] !== firstName[0].toUpperCase()) {
                    alert("La première lettre du prénom doit être en majuscule !");
                    $('#edit-first-name').focus();
                    return false;
                }
                
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(phone)) {
                    alert("Le numéro de téléphone doit contenir exactement 10 chiffres !");
                    $('#edit-phone').focus();
                    return false;
                }
                
                const password = $('#edit-password').val();
                if (password !== '') {
                    const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{8,}$/;
                    if (!passwordRegex.test(password)) {
                        alert("Le mot de passe doit contenir au moins :\n- 8 caractères\n- Une lettre majuscule\n- Une lettre minuscule\n- Un chiffre\n- Un caractère spécial");
                        $('#edit-password').focus();
                        return false;
                    }
                }
            }
        });

        // Validation en temps réel pour les champs de texte
        $('#edit-last-name, #edit-first-name').on('blur', function() {
            const value = $(this).val().trim();
            const field = $(this).attr('id') === 'edit-last-name' ? 'nom' : 'prénom';
            
            if (/\d/.test(value) || /\s/.test(value)) {
                $(this).addClass('is-invalid');
                $(`#${$(this).attr('id')}-error`).text(`Le ${field} ne doit pas contenir de chiffres ou espaces`).show();
            } else if (value !== '' && value[0] !== value[0].toUpperCase()) {
                $(this).addClass('is-invalid');
                $(`#${$(this).attr('id')}-error`).text(`La première lettre du ${field} doit être en majuscule`).show();
            } else {
                $(this).removeClass('is-invalid');
                $(`#${$(this).attr('id')}-error`).hide();
            }
        });

        // Validation du téléphone en temps réel
        $('#edit-phone').on('input', function() {
            let phone = $(this).val().replace(/\D/g, '');
            $(this).val(phone);
            
            const phoneRegex = /^[0-9]{10}$/;
            if (phone !== '' && !phoneRegex.test(phone)) {
                $(this).addClass('is-invalid');
                $('#edit-phone-error').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#edit-phone-error').hide();
            }
        });

        // Validation de la confirmation du mot de passe en temps réel
        $('#edit-confirm-password').on('input', function() {
            const password = $('#edit-password').val();
            const confirmPassword = $(this).val();
            
            if (password !== '' && confirmPassword !== password) {
                $(this).addClass('is-invalid');
                $('#edit-confirm-password-error').text('Les mots de passe ne correspondent pas').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#edit-confirm-password-error').hide();
            }
        });
    });
    </script>
</body>
</html>