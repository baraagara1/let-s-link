<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Covoiturage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <!-- Font Awesome -->
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
        /* Bordures vertes et rouges sans utiliser input:valid/invalid */
input.valid {
    border: 2px solid #28a745 !important; /* vert */
}

input.invalid {
    border: 2px solid #dc3545 !important; /* rouge */
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

<?php

session_start();

// ✅ Vérification que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.php");
    exit;
}

$idUtilisateurConnecte = $_SESSION['utilisateur_id'];
$currentPage = basename($_SERVER['PHP_SELF']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connexion échouée : " . $e->getMessage());
    }

    // Récupération des données
    $destination = trim($_POST['destination']);
    $date = $_POST['date'];
    $places = $_POST['place_dispo'];
    $prix_c = $_POST['prix_c'];
    $heure_depart = $_POST['heure_depart'];

    // ✅ On utilise uniquement l'ID de la session (ne jamais faire confiance à l'input hidden)
    $id_utilisateur = $idUtilisateurConnecte;

    $errors = [
        'destination' => '',
        'date' => '',
        'places' => '',
        'prix_c' => '',
        'heure_depart' => '',
    ];

    if (!is_numeric($prix_c) || $prix_c < 0) {
        $errors['prix_c'] = "Le prix doit être un nombre positif.";
    }

    if (!preg_match("/^[a-zA-Z\s]+$/", $destination)) {
        $errors['destination'] = "La destination ne doit contenir que des lettres et des espaces.";
    }

    $today = date('Y-m-d');
    if ($date < $today) {
        $errors['date'] = "La date doit être aujourd'hui ou une date future.";
    }

    if (!is_numeric($places) || $places < 1 || $places > 4) {
        $errors['places'] = "Le nombre de places doit être entre 1 et 4.";
    }

    if (empty(array_filter($errors))) {
        $sql = "INSERT INTO covoiturage (destination, date, place_dispo, id_utilisateur, prix_c, heure_depart)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$destination, $date, $places, $id_utilisateur, $prix_c, $heure_depart])) {
            header("Location: ajouter-covoiturage.php?success=1");
            exit();
        } else {
            $errors['destination'] = "Erreur lors de l'ajout du covoiturage.";
        }
    }
}
?>


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
    <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="logo.png" alt="logo" style="width:140px;">
    </a>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="index.html" class="nav-item nav-link <?= ($currentPage == 'index.html') ? 'text-warning' : '' ?>">Accueil</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle <?= ($currentPage == 'ajouter-covoiturage.php' || $currentPage == 'lister-covoiturages.php') ? 'text-warning' : '' ?>" data-bs-toggle="dropdown">Covoiturage</a>
                <div class="dropdown-menu bg-light m-0">
                    <a href="ajouter-covoiturage.php" class="dropdown-item <?= ($currentPage == 'ajouter-covoiturage.php') ? 'active text-white bg-success' : '' ?>">Ajouter un covoiturage</a>
                    <a href="lister-covoiturages.php" class="dropdown-item <?= ($currentPage == 'lister-covoiturages.php') ? 'active text-white bg-success' : '' ?>">Consulter les covoiturages</a>
                </div>
            </div>
            <a href="contact.html" class="nav-item nav-link <?= ($currentPage == 'contact.html') ? 'text-warning' : '' ?>">Contact</a>
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

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success text-center">Covoiturage ajouté avec succès !</div>
                <?php endif; ?>

                <form action="ajouter-covoiturage.php" method="POST" class="row g-3" autocomplete="off">
                    <div class="col-md-6">
                        <label for="destination" class="form-label">Destination</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="destination" class="form-control" value="<?= isset($destination) ? htmlspecialchars($destination) : '' ?>">
                        </div>
                        <?php if (!empty($errors['destination'])): ?>
                            <small class="text-danger"><?= $errors['destination'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="date" class="form-label">Date</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" name="date" class="form-control" value="<?= isset($date) ? htmlspecialchars($date) : '' ?>">
                        </div>
                        <?php if (!empty($errors['date'])): ?>
                            <small class="text-danger"><?= $errors['date'] ?></small>
                        <?php endif; ?>
                    </div>


                    <!-- 🚨 Cette ligne force un retour à la ligne -->


                    <div class="col-md-6">
                        <label for="place_dispo" class="form-label">Places Disponibles</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                            <input type="text" name="place_dispo" class="form-control" value="<?= isset($places) ? htmlspecialchars($places) : '' ?>">
                        </div>
                        <?php if (!empty($errors['places'])): ?>
                            <small class="text-danger"><?= $errors['places'] ?></small>
                        <?php endif; ?>
                    </div>

                    <!-- 🚨 Cette ligne force un retour à la ligne -->

                   
                    <div class="col-md-6">
    <label for="heure_depart" class="form-label">Heure de départ</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-clock"></i></span>
        <input type="time" name="heure_depart" class="form-control" value="<?= isset($heure_depart) ? htmlspecialchars($heure_depart) : '' ?>">
    </div>
    <?php if (!empty($errors['heure_depart'])): ?>
        <small class="text-danger"><?= $errors['heure_depart'] ?></small>
    <?php endif; ?>
</div>

                    

                  



<div class="col-md-6">
                        <label for="prix_c" class="form-label">Prix (DT)</label>
                        <div class="input-group">
                            <span class="input-group-text">DT</span>
                            <input type="text" name="prix_c" class="form-control" value="<?= isset($prix_c) ? htmlspecialchars($prix_c) : '' ?>">
                        </div>
                        <?php if (!empty($errors['prix_c'])): ?>
                            <small class="text-danger"><?= $errors['prix_c'] ?></small>
                        <?php endif; ?>
                    </div>






                    <input type="hidden" name="id_utilisateur" value="<?= $_SESSION['utilisateur_id'] ?>">



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



<section class="py-5" id="why-choose-us">
    <div class="container">
        <h3 class="text-center mb-4 fw-bold">Pourquoi nous choisir ?</h3>
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="p-4 border rounded shadow hover-effect">
                    <i class="fas fa-car fa-2x mb-3 text-primary"></i>
                    <h5 class="fw-bold">Facilité d’utilisation</h5>
                    <p class="text-muted">Ajoutez ou consultez un covoiturage en quelques clics.</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="p-4 border rounded shadow hover-effect">
                    <i class="fas fa-shield-alt fa-2x mb-3 text-success"></i>
                    <h5 class="fw-bold">Sécurité garantie</h5>
                    <p class="text-muted">Vos données et vos trajets sont protégés.</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="p-4 border rounded shadow hover-effect">
                    <i class="fas fa-mobile-alt fa-2x mb-3 text-warning"></i>
                    <h5 class="fw-bold">Responsive</h5>
                    <p class="text-muted">Accessible sur tous vos appareils.</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="p-4 border rounded shadow hover-effect">
                    <i class="fas fa-headset fa-2x mb-3 text-danger"></i>
                    <h5 class="fw-bold">Support rapide</h5>
                    <p class="text-muted">Une équipe à votre écoute 7j/7.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .hover-effect {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-effect:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
</style>


<!-- Footer -->
<footer class="bg-dark text-light pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5 class="text-white">Notre Adresse</h5>
                <p>123 Rue, Ville, Pays</p>
                <p>+216 99 999 999</p>
                <p>email@example.com</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="text-white">Suivez-nous</h5>
                <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-twitter"></i></a>
                <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <hr class="bg-light">
        <p class="text-center text-light mb-0">&copy; 2025 Let's Link. Tous droits réservés.</p>
    </div>
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
    const form = document.querySelector("form");
    const submitBtn = form.querySelector("button[type='submit']");
    const originalBtnHTML = submitBtn.innerHTML;

    form.addEventListener("submit", () => {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>Chargement...`;
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const destination = document.querySelector('input[name="destination"]');
    const date = document.querySelector('input[name="date"]');
    const places = document.querySelector('input[name="place_dispo"]');

    // Contrôle à la volée
    destination.addEventListener('input', () => {
        const regex = /^[a-zA-Z\s]+$/;
        toggleValidation(destination, regex.test(destination.value));
    });

    date.addEventListener('input', () => {
        const today = new Date().toISOString().split('T')[0];
        toggleValidation(date, date.value >= today);
    });

    places.addEventListener('input', () => {
        const val = parseInt(places.value);
        toggleValidation(places, val >= 1 && val <= 4);
    });

    

    function toggleValidation(input, isValid) {
        input.classList.remove('valid', 'invalid');
        input.classList.add(isValid ? 'valid' : 'invalid');
    }
});
</script>


</body>
</html>
