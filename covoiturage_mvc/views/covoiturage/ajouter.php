<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Covoiturage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: linear-gradient(135deg, #f1f2f6, #dff9fb);
        }
        .form-animate {
            animation: fadeInUp 0.8s ease-in-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        input.valid {
            border: 2px solid #28a745 !important;
        }
        input.invalid {
            border: 2px solid #dc3545 !important;
            animation: shake 0.3s;
        }
        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }
    </style>
</head>
<body>

<!-- Topbar -->
<div class="container-fluid bg-dark text-light px-0 py-2">
    <div class="row gx-0 d-none d-lg-flex">
        <div class="col-lg-7 px-5 text-start">
            <div class="h-100 d-inline-flex align-items-center me-4">
                <i class="fa fa-phone-alt me-2"></i><span>+126 34 567 289</span>
            </div>
            <div class="h-100 d-inline-flex align-items-center">
                <i class="far fa-envelope me-2"></i><span>PureVibe@gmail.com</span>
            </div>
        </div>
        <div class="col-lg-5 px-5 text-end">
            <span>Suivez-nous :</span>
            <a class="btn btn-link text-light" href="#"><i class="fab fa-facebook-f"></i></a>
            <a class="btn btn-link text-light" href="#"><i class="fab fa-twitter"></i></a>
            <a class="btn btn-link text-light" href="#"><i class="fab fa-linkedin-in"></i></a>
            <a class="btn btn-link text-light" href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
    <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="logo.png" alt="logo" style="width:140px;">
    </a>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="index.php" class="nav-item nav-link">Accueil</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Covoiturage</a>
                <div class="dropdown-menu bg-light m-0">
                    <a href="?action=ajouter" class="dropdown-item active">Ajouter un covoiturage</a>
                    <a href="?action=lister" class="dropdown-item">Consulter les covoiturages</a>
                </div>
            </div>
            <a href="contact.html" class="nav-item nav-link">Contact</a>
        </div>
        <a href="#" class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block" style="background-color: #090068; border: none;">Se connecter <i class="fa fa-arrow-right ms-3"></i></a>
    </div>
</nav>

<!-- Formulaire -->
<div class="container py-5 form-animate">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg p-4 rounded">
                <h2 class="text-center mb-4 text-dark">
                    <i class="fas fa-car-side me-2 text-primary"></i>Ajouter un Covoiturage
                </h2>
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success text-center">
        ✅ Covoiturage ajouté avec succès !
    </div>
<?php endif; ?>


                <form action="?action=ajouter" method="POST" class="row g-3" autocomplete="off">
                    <!-- Lieu de départ -->
                    <div class="col-md-6">
                        <label class="form-label">Lieu de départ</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-location-arrow"></i></span>
                            <input type="text" name="lieu_depart"
                                   class="form-control <?= isset($errors['lieu_depart']) && $errors['lieu_depart'] ? 'invalid' : (isset($data['lieu_depart']) ? 'valid' : '') ?>"
                                   value="<?= htmlspecialchars($data['lieu_depart'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['lieu_depart'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['lieu_depart'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Destination -->
                    <div class="col-md-6">
                        <label class="form-label">Destination</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="destination"
                                   class="form-control <?= isset($errors['destination']) && $errors['destination'] ? 'invalid' : (isset($data['destination']) ? 'valid' : '') ?>"
                                   value="<?= htmlspecialchars($data['destination'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['destination'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['destination'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Date -->
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" name="date"
                                   class="form-control <?= isset($errors['date']) && $errors['date'] ? 'invalid' : (isset($data['date']) ? 'valid' : '') ?>"
                                   value="<?= htmlspecialchars($data['date'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['date'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['date'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Heure de départ -->
                    <div class="col-md-6">
                        <label class="form-label">Heure de départ</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="time" name="heure_depart"
                                   class="form-control <?= isset($errors['heure_depart']) && $errors['heure_depart'] ? 'invalid' : (isset($data['heure_depart']) ? 'valid' : '') ?>"
                                   value="<?= htmlspecialchars($data['heure_depart'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['heure_depart'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['heure_depart'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Places disponibles -->
                    <div class="col-md-6">
                        <label class="form-label">Places Disponibles</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                            <input type="text" name="place_dispo"
                                   class="form-control <?= isset($errors['place_dispo']) && $errors['place_dispo'] ? 'invalid' : (isset($data['place_dispo']) ? 'valid' : '') ?>"
                                   value="<?= htmlspecialchars($data['place_dispo'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['place_dispo'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['place_dispo'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Prix -->
                    <div class="col-md-6">
                        <label class="form-label">Prix (DT)</label>
                        <div class="input-group">
                            <span class="input-group-text">DT</span>
                            <input type="text" name="prix_c"
                                   class="form-control <?= isset($errors['prix_c']) && $errors['prix_c'] ? 'invalid' : (isset($data['prix_c']) ? 'valid' : '') ?>"
                                   value="<?= htmlspecialchars($data['prix_c'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['prix_c'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['prix_c'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Bouton -->
                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary px-5 py-2">
                            <i class="fas fa-plus-circle me-2"></i>Ajouter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
