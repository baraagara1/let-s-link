
    <?php
    require_once 'C:\xampp\htdocs\mon_project_web\model\utilisateur.php';
    require_once 'C:\xampp\htdocs\mon_project_web\controller\utilisateurC.php';

    $message = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            isset($_POST['last-name']) && isset($_POST['first-name']) && isset($_POST['email']) &&
            isset($_POST['phone']) && isset($_POST['address']) && isset($_POST['password']) &&
            isset($_POST['role'])
        ) {
            $nom = trim($_POST['last-name']);
            $prenom = trim($_POST['first-name']);
            $email = trim($_POST['email']);
            $telephone = intval($_POST['phone']);
            $adresse = trim($_POST['address']);
            $motpasse = $_POST['password'];
            $role = $_POST['role'];

            $utilisateur = new Utilisateur($nom, $prenom, $email, $telephone, $adresse, $motpasse, $role);
            $utilisateurC = new UtilisateurC();

            try {
                $utilisateurC->ajouterUtilisateur($utilisateur);
                $message = "✅ Utilisateur ajouté avec succès.";
                header("Location: afficher.php"); // Redirection automatique
                exit;
            } catch (Exception $e) {
                $message = "❌ Erreur lors de l'ajout : " . $e->getMessage();
            }
        } else {
            $message = "❌ Veuillez remplir tous les champs.";
        }
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

    <title>Pure Vibe - Ajouter Utilisateur</title>
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
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
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
        .phone-input {
            position: relative;
        }
        .phone-prefix {
            position: absolute;
            left: 10px;
            top: 38px;
            color: #6c757d;
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
                        <a class="collapse-item active" href="ajouter.html">
                            <i class="fas fa-plus-circle mr-2"></i>Ajouter
                        </a>
                        <a class="collapse-item" href="modifier.php">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                        <a class="collapse-item" href="supprimer.html">
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
<?php if (!empty($message)) echo "<div class='alert alert-success'>$message</div>"; ?>
                        <h1 class="h3 mb-0 text-gray-800">Ajouter un Utilisateur</h1>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4 dynamic-frame">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Formulaire d'ajout</h6>
                                </div>
                                <div class="card-body">
                                    <form id="add-user-form" action="ajouter.php" method="POST">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Nom <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="last-name" name="last-name" required>
                                                <div class="error-message" id="last-name-error">Veuillez entrer un nom valide</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label>Prénom <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="first-name" name="first-name" required>
                                                <div class="error-message" id="first-name-error">Veuillez entrer un prénom valide</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                                <div class="error-message" id="email-error">Veuillez entrer une adresse email valide</div>
                                            </div>
                                            <div class="col-md-6 mb-3 phone-input">
                                                <label>Téléphone <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control pl-4" id="phone" name="phone" pattern="[0-9]{9}" required>
                                                <div class="error-message" id="phone-error">Veuillez entrer un numéro valide (8 chiffres sans le 0)</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Rôle <span class="text-danger">*</span></label>
                                                <select class="form-control" id="role" name="role" required>
                                                    <option value="">Sélectionner...</option>
                                                    <option value="admin">Administrateur</option>
                                                    <option value="user">Utilisateur</option>
                                                </select>
                                                <div class="error-message" id="role-error">Veuillez sélectionner un rôle</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label>Mot de passe <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="password" name="password" required>
                                                <div class="password-strength">
                                                    <div class="password-strength-bar" id="password-strength-bar"></div>
                                                </div>
                                                <div class="error-message" id="password-error">
                                                    Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Confirmer le mot de passe <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                                                <div class="error-message" id="confirm-password-error">Les mots de passe ne correspondent pas</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label>Adresse <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="address" name="address" required>
                                                <div class="error-message" id="address-error">Veuillez entrer une adresse valide</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                                <i class="fas fa-save mr-1"></i> Enregistrer
                                            </button>
                                        </div>
                                    </form>
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
    $(document).ready(function() {
        // Formatage automatique du téléphone
        $('#phone').on('input', function() {
            let phone = $(this).val().replace(/\D/g, '');
            $(this).val(phone);
        });

        // Validation du formulaire
        function validateForm() {
            let isValid = true;

            // Validation du nom
            const lastName = $('#last-name').val().trim();
            if (lastName === '') {
                $('#last-name').addClass('is-invalid');
                $('#last-name-error').text('Veuillez entrer un nom valide').show();
                isValid = false;
            } else if (/\d/.test(lastName) || /\s/.test(lastName)) {
                $('#last-name').addClass('is-invalid');
                $('#last-name-error').text('Le nom ne doit pas contenir de chiffres ou espaces').show();
                isValid = false;
            } else if (lastName[0] !== lastName[0].toUpperCase()) {
                $('#last-name').addClass('is-invalid');
                $('#last-name-error').text('La première lettre doit être en majuscule').show();
                isValid = false;
            } else {
                $('#last-name').removeClass('is-invalid');
                $('#last-name-error').hide();
            }

            // Validation du prénom
            const firstName = $('#first-name').val().trim();
            if (firstName === '') {
                $('#first-name').addClass('is-invalid');
                $('#first-name-error').text('Veuillez entrer un prénom valide').show();
                isValid = false;
            } else if (/\d/.test(firstName) || /\s/.test(firstName)) {
                $('#first-name').addClass('is-invalid');
                $('#first-name-error').text('Le prénom ne doit pas contenir de chiffres ou espaces').show();
                isValid = false;
            } else if (firstName[0] !== firstName[0].toUpperCase()) {
                $('#first-name').addClass('is-invalid');
                $('#first-name-error').text('La première lettre doit être en majuscule').show();
                isValid = false;
            } else {
                $('#first-name').removeClass('is-invalid');
                $('#first-name-error').hide();
            }

            // Validation de l'email
            const email = $('#email').val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === '' || !emailRegex.test(email)) {
                $('#email').addClass('is-invalid');
                $('#email-error').show();
                isValid = false;
            } else {
                $('#email').removeClass('is-invalid');
                $('#email-error').hide();
            }

            // Validation du téléphone
            const phone = $('#phone').val().trim();
            const phoneRegex = /^[0-9]{9}$/;
            if (phone === '' || !phoneRegex.test(phone)) {
                $('#phone').addClass('is-invalid');
                $('#phone-error').show();
                isValid = false;
            } else {
                $('#phone').removeClass('is-invalid');
                $('#phone-error').hide();
            }

            // Validation du rôle
            const role = $('#role').val();
            if (role === '') {
                $('#role').addClass('is-invalid');
                $('#role-error').show();
                isValid = false;
            } else {
                $('#role').removeClass('is-invalid');
                $('#role-error').hide();
            }

            // Validation du mot de passe
            const password = $('#password').val();
            const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            if (password === '') {
                $('#password').addClass('is-invalid');
                $('#password-error').text('Veuillez entrer un mot de passe').show();
                isValid = false;
            } else if (!passwordRegex.test(password)) {
                $('#password').addClass('is-invalid');
                $('#password-error').text('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre').show();
                isValid = false;
            } else {
                $('#password').removeClass('is-invalid');
                $('#password-error').hide();
            }

            // Validation de la confirmation du mot de passe
            const confirmPassword = $('#confirm-password').val();
            if (confirmPassword === '') {
                $('#confirm-password').addClass('is-invalid');
                $('#confirm-password-error').text('Veuillez confirmer le mot de passe').show();
                isValid = false;
            } else if (confirmPassword !== password) {
                $('#confirm-password').addClass('is-invalid');
                $('#confirm-password-error').text('Les mots de passe ne correspondent pas').show();
                isValid = false;
            } else {
                $('#confirm-password').removeClass('is-invalid');
                $('#confirm-password-error').hide();
            }

            // Validation de l'adresse
            const address = $('#address').val().trim();
            if (address === '') {
                $('#address').addClass('is-invalid');
                $('#address-error').text('Veuillez entrer une adresse valide').show();
                isValid = false;
            } else if (address.length < 5) {
                $('#address').addClass('is-invalid');
                $('#address-error').text('L\'adresse doit contenir au moins 5 caractères').show();
                isValid = false;
            } else {
                $('#address').removeClass('is-invalid');
                $('#address-error').hide();
            }

            return isValid;
        }

        // Écouteur pour la force du mot de passe
        $('#password').on('input', function() {
            const password = $(this).val();
            const strengthBar = $('#password-strength-bar');
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
        $('#add-user-form').on('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 500);
            } else {
                // Simulation d'envoi réussi
                alert('Utilisateur ajouté avec succès!');
                // this.submit(); // Décommentez pour l'envoi réel
            }
        });

        // Validation en temps réel pour chaque champ
        $('#last-name, #first-name').on('blur', function() {
            const value = $(this).val().trim();
            const fieldId = $(this).attr('id');
            const fieldName = fieldId === 'last-name' ? 'nom' : 'prénom';
            
            if (value === '') {
                $(this).removeClass('is-invalid');
                $(`#${fieldId}-error`).hide();
                return;
            }
            
            if (/\d/.test(value) || /\s/.test(value)) {
                $(this).addClass('is-invalid');
                $(`#${fieldId}-error`).text(`Le ${fieldName} ne doit pas contenir de chiffres ou espaces`).show();
            } else if (value[0] !== value[0].toUpperCase()) {
                $(this).addClass('is-invalid');
                $(`#${fieldId}-error`).text(`La première lettre du ${fieldName} doit être en majuscule`).show();
            } else {
                $(this).removeClass('is-invalid');
                $(`#${fieldId}-error`).hide();
            }
        });

        // Validation des autres champs en temps réel
        $('#email, #phone, #role, #password, #confirm-password, #address').on('blur', function() {
            validateForm();
        });

        // Validation de la confirmation du mot de passe en temps réel
        $('#confirm-password').on('input', function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();
            
            if (confirmPassword === '') {
                $(this).addClass('is-invalid');
                $('#confirm-password-error').text('Veuillez confirmer le mot de passe').show();
            } else if (confirmPassword !== password) {
                $(this).addClass('is-invalid');
                $('#confirm-password-error').text('Les mots de passe ne correspondent pas').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#confirm-password-error').hide();
            }
        });

        // Validation de l'adresse en temps réel
        $('#address').on('blur', function() {
            const address = $(this).val().trim();
            
            if (address === '') {
                $(this).addClass('is-invalid');
                $('#address-error').text('Veuillez entrer une adresse valide').show();
            } else if (address.length < 5) {
                $(this).addClass('is-invalid');
                $('#address-error').text('L\'adresse doit contenir au moins 5 caractères').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#address-error').hide();
            }
        });
    });
    </script>
</body>
</html>