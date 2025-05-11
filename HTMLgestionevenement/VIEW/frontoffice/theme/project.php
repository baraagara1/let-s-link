<?php include 'add-event.php'; ?>
<?php
// Vérifier si la session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../controller/EventController.php';
require_once '../../../controller/ActiviteController.php';
require_once '../../../controller/FavorisController.php';

$controller = new EventController();
$activiteController = new ActiviteController();
$allActivites = $activiteController->listActivites();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['id'];

$eventsPerPage = 4;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $eventsPerPage;

if (isset($_GET['id_a']) && $_GET['id_a'] !== '') {
    $id_a = intval($_GET['id_a']);
    $allEvents = $controller->getEventsByActivite($id_a);
    $totalEvents = count($allEvents);
    $events = array_slice($allEvents, $offset, $eventsPerPage);
} else {
    $totalEvents = $controller->countAllEvents();
    $events = $controller->getPaginatedEvents($offset, $eventsPerPage);
}

$totalPages = ceil($totalEvents / $eventsPerPage);

require_once '../../../controller/FavorisController.php';
$favorisController = new FavorisController();
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Nos Activites</title>
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

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (nécessaire pour Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Styles pour les images de même taille */
        .card-img-container {
            width: 100%;
            height: 250px;
            overflow: hidden;
            position: relative;
        }
        
        .card-img-top {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        /* Effet de hover inversé */
        .portfolio-item {
            position: relative;
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }
        
        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: all 0.4s ease;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }
        
        .portfolio-item:hover .card-overlay {
            opacity: 1;
        }
        
        .portfolio-item:hover .card-img-top {
            transform: scale(1.1);
        }
        
        /* Style des cartes */
        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .portfolio-item:hover .card {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        /* Boutons d'action */
        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            transition: all 0.3s ease;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }
        
        .btn-edit {
            background: #FFC107;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .btn-delete {
            background: #DC3545;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .btn-view {
            background: #17A2B8;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .btn-action:hover {
            transform: scale(1.1) translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Style du texte */
        .event-title {
            color: #06BBCC;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .event-info {
            color: #555;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .event-info i {
            color: #06BBCC;
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }
        
        /* Style pour le select de filtrage */
        .filter-select {
            width: 250px;
            border: 2px solid #06BBCC;
            border-radius: 25px;
            padding: 10px 20px;
            outline: none;
            transition: all 0.3s;
        }
        
        .filter-select:focus {
            box-shadow: 0 0 0 3px rgba(6, 187, 204, 0.2);
        }
        
        /* Badge pour les événements à venir */
        .event-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #28A745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Animation des éléments */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .portfolio-item {
            animation: fadeIn 0.6s ease forwards;
            animation-delay: calc(var(--order) * 0.1s);
            opacity: 0;
        }             
        .event-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    height: auto;
    padding: 20px;
    transition: all 0.3s ease;
}

.event-card:hover {
    transform: translateY(-5px);
}

.event-details {
    margin-bottom: 15px;
}
        
    .event-title {
        font-size: 24px;
        font-weight: bold;
        color: #06BBCC;
        margin-bottom: 10px;
    }
    .event-info {
        font-size: 18px;
        color: #555;
        margin-bottom: 10px;
    }
        .event-description {
            color: #555;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .event-no-offer {
            color: #888;
            font-style: italic;
        }
        .event-image {
        flex: 1;
        overflow: hidden;
        position: relative;
    }  
    .event-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}
    .event-card:hover .event-image img {
        transform: scale(1.2);
    }

        
        .action-buttons a {
        display: inline-block;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        margin-right: 10px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

        
    .action-buttons {
        margin-top: 10px;
    }
        
        .btn-view {
            background: #17A2B8;
            color: white;
            border: none;
        }
        
        .btn-edit {
            background: #FFC107;
            color: white;
            border: none;
        }
        
        .btn-delete {
            background: #DC3545;
            color: white;
            border: none;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        /* Style pour le filtre */
        .filter-container {
        margin: 30px 0;
        text-align: center;
    }
        
        .filter-select {
        padding: 10px 20px;
        border-radius: 25px;
        border: 2px solid #06BBCC;
        width: 300px;
        max-width: 100%;
    }
    </style>
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
  <i class="fas fa-plus me-2"></i>Ajouter une activitées
</button>

        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white mb-4 animated slideInDown">Nos Activites</h1>
            <?php 
if (isset($id_a)) {
    $activite = $activiteController->getActiviteById($id_a);
    if ($activite) {
        echo '<h4 class="text-light">' . htmlspecialchars($activite['nom_a']) . '</h4>';
    }
}
?>
            
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nos Activitées</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Events Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 700px;">
                <p class="fs-5 fw-bold text-primary">Nos Activitées</p>
                <h1 class="display-5 mb-4">Découvrez Nos Activitées </h1>
            </div>
            
            <div class="d-flex justify-content-center mt-3 mb-5">
                <form method="GET" action="project.php" class="w-100 text-center">
                <select name="id_a" class="form-select filter-select select2" onchange="this.form.submit()">
    <option value="">Toutes les activités</option>
    <?php foreach ($allActivites as $activite): ?>
        <option value="<?= $activite['id_a'] ?>" <?= (isset($id_a) && $id_a == $activite['id_a']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($activite['nom_a']) ?>
        </option>
    <?php endforeach; ?>
</select>

                </form>
            </div>
            <form method="GET" action="suggestions.php" class="d-flex justify-content-center my-4">
            <input type="hidden" name="id" value="<?= $_SESSION['id'] ?>">

    <button type="submit" class="btn btn-info">Voir mon historique</button>
</form>

<a href="suggest_event.php" class="btn btn-success">Suggestion</a>


            <!-- Liste des événements -->
<div class="container">
    <div class="row">
        <!-- Pagination -->
        <?php //var_dump($events); ?>

        <?php foreach ($events as $event): ?>
    <div class="col-lg-4 col-md-6">
        <div class="event-card text-center">
            <div class="event-details">
                <div class="event-title"><?= htmlspecialchars($event['nom_e']) ?></div>
                <div class="event-info"><i class="far fa-calendar-alt"></i> <?= htmlspecialchars($event['date_e']) ?></div>
                <div class="event-info"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['lieu_e']) ?></div>
                
                <?php if ($_SESSION['id'] == $event['id_u']): ?>
                    <!-- Afficher les boutons de modification et suppression uniquement si l'utilisateur est le créateur -->
                    <div class="action-buttons d-flex justify-content-center mt-3">
                        <a href="modifier-event.php?id=<?= $event['id_e'] ?>" class="btn-edit">Modifier</a>
                        <a href="supprimer-event.php?id=<?= $event['id_e'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet événement ?')">Supprimer</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="event-image mt-3">
                <img src="../../uploads/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['nom_e']) ?>">
            </div>
            
            <!-- Formulaire de participation -->
            <form method="GET" action="participer.php">
                <input type="hidden" name="id_e" value="<?= $event['id_e'] ?>">  <!-- ID de l'événement -->
                <input type="hidden" name="id" value="<?= $_SESSION['id'] ?>">  <!-- ID de l'utilisateur -->
                <button type="submit" class="btn btn-success">Participer</button>
            </form>

            <!-- Formulaire pour ajouter aux favoris -->
            <form method="POST" action="toggle_favori.php" class="mt-2">
                <input type="hidden" name="id_e" value="<?= $event['id_e'] ?>">
                <input type="hidden" name="id" value="<?= $_SESSION['id'] ?>">
                <button type="submit" class="btn <?=$favorisController->verifier($_SESSION['id'], $event['id_e']) ? 'btn-danger' : 'btn-outline-danger' ?>">
                    <i class="fa<?= $favorisController->verifier($_SESSION['id'], $event['id_e']) ? 's' : 'r' ?> fa-heart"></i>
                    <?= $favorisController->verifier($_SESSION['id'], $event['id_e']) ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
                </button>
            </form>
        </div>
    </div>
<?php endforeach; ?>


<nav aria-label="Page navigation">
<ul class="pagination justify-content-center">
  <?php if ($currentPage > 1): ?>
    <li class="page-item">
      <a class="page-link" href="?<?= (isset($_GET['id_a']) && $_GET['id_a'] !== '') ? 'id_a=' . intval($_GET['id_a']) . '&' : '' ?>page=<?= $currentPage - 1 ?>">Précédent</a>
    </li>
  <?php endif; ?>

  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
      <a class="page-link" href="?<?= (isset($_GET['id_a']) && $_GET['id_a'] !== '') ? 'id_a=' . intval($_GET['id_a']) . '&' : '' ?>page=<?= $i ?>"><?= $i ?></a>
    </li>
  <?php endfor; ?>

  <?php if ($currentPage < $totalPages): ?>
    <li class="page-item">
      <a class="page-link" href="?<?= (isset($_GET['id_a']) && $_GET['id_a'] !== '') ? 'id_a=' . intval($_GET['id_a']) . '&' : '' ?>page=<?= $currentPage + 1 ?>">Suivant</a>
    </li>
  <?php endif; ?>
</ul>

</nav>

    </div>
</div>

            </div>
        </div>
    </div>
    <!-- Events End -->

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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialise les animations
        if (typeof WOW === 'function') {
            new WOW().init();
        }
        
        // Confirmation pour la suppression
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet Activitée ?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Animation au scroll
        const animateOnScroll = function() {
            const elements = document.querySelectorAll('.portfolio-item');
            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.2;
                
                if (elementPosition < screenPosition) {
                    element.style.opacity = '1';
                }
            });
        };

        window.addEventListener('scroll', animateOnScroll);
        animateOnScroll(); // Initial call
    });
    </script>
    <!-- MODALE : Formulaire d'ajout d'événement -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
  <div class="modal-content p-4">
    <h4 class="mb-4">Ajouter une activité</h4>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

<!-- Formulaire d'ajout d'événement -->
<form method="POST" enctype="multipart/form-data" onsubmit="return validateModalForm(event)">
    <!-- Champ caché pour l'ID utilisateur -->
    <input type="hidden" name="id" value="<?= $_SESSION['id'] ?? '' ?>">

    <div class="mb-3">
        <label>Nom de l'activité</label>
        <input type="text" name="event-name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Date</label>
        <input type="date" name="event-date" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Lieu</label>
        <input type="text" name="event-location" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Image</label>
        <input type="file" name="event-image" class="form-control" accept="image/*" required>
    </div>

    <div class="mb-3">
        <label>Choisir une activité</label>
        <select name="event-id-a" class="form-control" required>
            <option value="">Sélectionnez une activité</option>
            <?php foreach ($allActivites as $activite): ?>
                <option value="<?= $activite['id_a'] ?>"><?= htmlspecialchars($activite['nom_a']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </div>
</form>



    </div>
  </div>
</div>
<script>
function validateModalForm(event) {
    const nom = document.querySelector('input[name="event-name"]').value.trim();
    const dateStr = document.querySelector('input[name="event-date"]').value.trim();
    const lieu = document.querySelector('input[name="event-location"]').value.trim();
    const image = document.querySelector('input[name="event-image"]').files[0];

    if (!nom || !dateStr || !lieu || !image) {
        alert("Veuillez remplir tous les champs.");
        event.preventDefault();
        return false;
    }

    if (/\d/.test(nom)) {
        alert("Le nom de l'activitées ne doit pas contenir de chiffres.");
        event.preventDefault();
        return false;
    }

    const date = new Date(dateStr);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    if (isNaN(date.getTime()) || date <= today) {
        alert("La date de l'activitées doit être dans le futur.");
        event.preventDefault();
        return false;
    }

    if (!image.type.startsWith("image/")) {
        alert("Le fichier sélectionné doit être une image.");
        event.preventDefault();
        return false;
    }

    return true;
}
</script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Choisir une activité",
        allowClear: true,
        width: 'resolve'
    });
});
</script>





<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info text-center">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>



</body>
</html>