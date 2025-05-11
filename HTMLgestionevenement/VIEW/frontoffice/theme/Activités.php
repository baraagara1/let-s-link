<?php
require_once '../../../controller/ActiviteController.php';
$controller = new ActiviteController();
$activites = $controller->listActivites();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Activités</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon & Fonts -->
    <link href="img/favicon.ico" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">  

    <!-- Icon Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries CSS -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Bootstrap & Main CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <!-- Topbar & Navbar -->
    <div class="container-fluid bg-dark text-light px-0 py-2">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center me-4"><span class="fa fa-phone-alt me-2"></span>+216 97640509</div>
                <div class="h-100 d-inline-flex align-items-center"><span class="far fa-envelope me-2"></span>letslink@gmail.com</div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center mx-n2">
                    <span>Suivez-nous :</span>
                    <a class="btn btn-link text-light" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-link text-light" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
        <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h1 class="m-0">User</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.html" class="nav-item nav-link">Accueil</a>
                <a href="activites.php" class="nav-item nav-link active">Activités</a>
                <a href="contact.html" class="nav-item nav-link">Contact</a>
                <a href="project.php" class="nav-item nav-link">project</a>
            </div>
            <a href="#" class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block">Rejoignez-nous<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white mb-4 animated slideInDown">Activités</h1>
        </div>
    </div>

    <!-- Section Activités -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <p class="fs-5 fw-bold text-primary">Nos Activités</p>
                <h1 class="display-6 mb-5">Découvrez ce que vous pouvez faire avec Let's Link</h1>
            </div>
            <div class="row g-4">
    <?php foreach ($activites as $index => $activite): ?>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="<?= 0.1 + ($index * 0.1) ?>s">
            <div class="service-item rounded d-flex h-100">
                <div class="service-img rounded">
                    <?php if (!empty($activite['image'])): ?>
                        <img class="img-fluid" src="../../uploads/<?= htmlspecialchars($activite['image']) ?>" alt="Image activité">
                    <?php else: ?>
                        <img class="img-fluid" src="img/default.jpg" alt="Activité">
                    <?php endif; ?>
                </div>
                <div class="service-text rounded p-5">
                    <h4 class="mb-3"><?= htmlspecialchars($activite['nom_a']) ?></h4>
                    <p class="mb-4"><?= htmlspecialchars($activite['description']) ?></p>
                    <a class="btn btn-sm" href="project.php?id_a=<?= $activite['id_a'] ?>">
                        <i class="fa fa-calendar text-primary me-2"></i>Voir les événements
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

        </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid bg-dark text-light footer mt-5 py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">Coordonnées</h4>
                    <p><i class="fa fa-map-marker-alt me-3"></i>Tunis</p>
                    <p><i class="fa fa-phone-alt me-3"></i>+216 97640509</p>
                    <p><i class="fa fa-envelope me-3"></i>letslink@gmail.com</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="container-fluid copyright py-4 bg-dark text-light">
        <div class="container text-center">
            &copy; 2025 Let's Link. Tous droits réservés.
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
