<?php
require_once '../../../controller/ParticipantController.php';
require_once '../../../config.php';
session_start();

$controller = new ParticipantController();

$id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id'];
$events = [];

if ($id) {
    $events = $controller->getEventsByUser($id);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique de participation</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">  

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <style>
        .history-container {
            padding: 50px 0;
        }
        
        .history-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .history-header h1 {
            color: #06BBCC;
            margin-bottom: 15px;
        }
        
        .history-header p {
            font-size: 18px;
            color: #555;
        }
        
        .history-icon {
            font-size: 50px;
            color: #06BBCC;
            margin-bottom: 20px;
        }
        
        .event-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .event-image {
            height: 250px;
            overflow: hidden;
        }
        
        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .event-card:hover .event-image img {
            transform: scale(1.1);
        }
        
        .event-content {
            padding: 20px;
        }
        
        .event-title {
            font-size: 22px;
            font-weight: bold;
            color: #06BBCC;
            margin-bottom: 10px;
        }
        
        .event-info {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }
        
        .event-info i {
            color: #06BBCC;
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }
        
        .action-buttons {
            margin-top: 15px;
        }
        
        .btn-action {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-view {
            background: #17A2B8;
        }
        
        .btn-view:hover {
            background: #138496;
        }
        
        .no-events {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #666;
        }
        
        .participated-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #28A745;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            z-index: 10;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid bg-dark text-light px-0 py-2">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <span class="fa fa-phone-alt me-2"></span>
                    <span>+012 345 6789</span>
                </div>
                <div class="h-100 d-inline-flex align-items-center">
                    <span class="far fa-envelope me-2"></span>
                    <span>let'slink@gmail.com</span>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center mx-n2">
                    <span>Follow Us:</span>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
        <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h1 class="m-0">USER</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.html?id=<?= $_SESSION['id'] ?>" class="nav-item nav-link">Home</a>
                <a href="Activités.php?id=<?= $_SESSION['id'] ?>" class="nav-item nav-link active">Activité</a>
                <a href="project.php?id=<?= $_SESSION['id'] ?>" class="nav-item nav-link active">Projects</a>
                <a href="contact.php?id=<?= $_SESSION['id'] ?>" class="nav-item nav-link">Contact</a>
                <a href="favoris.php?id=<?= $_SESSION['id'] ?>" class="nav-item nav-link">Mes Favoris</a>
            </div>
            <button class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <i class="fas fa-plus me-2"></i>Ajouter une activité
            </button>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white mb-4 animated slideInDown">Mon Historique</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Historique</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- History Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="history-header wow fadeInUp" data-wow-delay="0.1s">
                <div class="history-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h1 class="display-5 mb-3">Vos participations passées</h1>
                <p class="fs-5">Retrouvez tous les événements auxquels vous avez participé</p>
            </div>
            
            <div class="row">
                <?php if (empty($events)): ?>
                    <div class="col-12">
                        <div class="no-events wow fadeIn" data-wow-delay="0.2s">
                            <i class="fas fa-calendar-times fa-3x mb-3" style="color: #06BBCC;"></i>
                            <h3>Aucun événement trouvé</h3>
                            <p>Vous n'avez pas encore participé à des événements.</p>
                            <a href="Activités.php" class="btn btn-primary mt-3">Découvrir les événements</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.<?= ($i % 3) + 1 ?>s">
                            <div class="event-card">
                                <div class="participated-badge">
                                    <i class="fas fa-check-circle"></i> Participé
                                </div>
                                <div class="event-image">
                                    <img src="../../uploads/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['nom_e']) ?>">
                                </div>
                                <div class="event-content">
                                    <h3 class="event-title"><?= htmlspecialchars($event['nom_e']) ?></h3>
                                    <p class="event-info"><i class="far fa-calendar-alt"></i><?= htmlspecialchars($event['date_e']) ?></p>
                                    <p class="event-info"><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($event['lieu_e']) ?></p>
                                    
                                    <div class="action-buttons">
                                        <a href="event-details.php?id=<?= $event['id_e'] ?>" class="btn-action btn-view">
                                            <i class="far fa-eye me-1"></i> Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- History End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer mt-5 py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Our Office</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Tunis</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+216 97640509</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>lest'slink@gamil.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/parallax/parallax.min.js"></script>
    <script src="lib/isotope/isotope.pkgd.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>