<?php
require_once '../../../config.php';
require_once '../../../controller/EventController.php';
require_once '../../../controller/ParticipantController.php';

session_start();
$id = $_SESSION['id']; // ID utilisateur (par défaut à 5 pour les tests)

// OpenAI API Key
$apiKey = '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';

// 1. Récupérer les événements passés de l'utilisateur
$participantController = new ParticipantController();
$historique = $participantController->getEventsByUser($id);

// 2. Récupérer tous les événements futurs
$eventController = new EventController();
$tousLesEvenements = $eventController->listEvents();
$evenementsFuturs = array_filter($tousLesEvenements, function($e) {
    return $e['date_e'] > date('Y-m-d');
});

// 3. Générer le prompt
$historiqueTexte = "";
foreach ($historique as $e) {
    $historiqueTexte .= "- Titre: {$e['nom_e']}, Lieu: {$e['lieu_e']}, Date: {$e['date_e']}\n";
}

$evenementsFutursTexte = "";
foreach ($evenementsFuturs as $e) {
    $evenementsFutursTexte .= "- Titre: {$e['nom_e']}, Lieu: {$e['lieu_e']}, Date: {$e['date_e']}\n";
}

$prompt = "
Voici l'historique des événements auxquels l'utilisateur a participé :
$historiqueTexte

Voici une liste d'événements à venir :
$evenementsFutursTexte

En te basant uniquement sur les titres et les lieux des événements passés, sélectionne les événements à venir qui pourraient intéresser cet utilisateur. 
Réponds uniquement avec un tableau JSON d'événements à recommander, avec les clés exactes : titre, lieu, date.
";

// 4. Appel à l'API OpenAI
$data = [
    'model' => 'gpt-4o',
    'messages' => [
        ['role' => 'system', 'content' => 'Tu es un assistant qui recommande des événements basés sur les préférences passées.'],
        ['role' => 'user', 'content' => $prompt]
    ],
    'temperature' => 0.7
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

// Traitement de la réponse
$suggestions = [];
if ($response) {
    $result = json_decode($response, true);
    $content = trim(preg_replace('/^```json|```$/i', '', $result['choices'][0]['message']['content']));
    $suggestions = json_decode($content, true) ?? [];
}

// Récupérer les détails complets des événements suggérés
$suggestedEventsDetails = [];
foreach ($suggestions as $suggestion) {
    foreach ($evenementsFuturs as $event) {
        if ($event['nom_e'] === $suggestion['titre'] && 
            $event['lieu_e'] === $suggestion['lieu'] && 
            $event['date_e'] === $suggestion['date']) {
            $suggestedEventsDetails[] = $event;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suggestions personnalisées</title>
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
    
    <style>
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
        
        .action-buttons {
            margin-top: 10px;
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
        
        .btn-view {
            background: #17A2B8;
        }
        
        .btn-participate {
            background: #28A745;
        }
        
        .no-suggestions {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #666;
        }
        
        .suggestions-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .suggestions-header h1 {
            color: #06BBCC;
            margin-bottom: 15px;
        }
        
        .suggestions-header p {
            font-size: 18px;
            color: #555;
        }
        
        .ai-icon {
            font-size: 50px;
            color: #06BBCC;
            margin-bottom: 20px;
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
                <i class="fas fa-plus me-2"></i>Ajouter une activitées
            </button>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white mb-4 animated slideInDown">Suggestions Personnalisées</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Suggestions IA</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Suggestions Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="suggestions-header wow fadeInUp" data-wow-delay="0.1s">
                <div class="ai-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <h1 class="display-5 mb-3">Nos suggestions pour vous</h1>
                <p class="fs-5">Basées sur vos participations passées</p>
            </div>
            
            <div class="row">
                <?php if (empty($suggestedEventsDetails)): ?>
                    <div class="col-12">
                        <div class="no-suggestions">
                            <i class="fas fa-info-circle fa-3x mb-3" style="color: #06BBCC;"></i>
                            <h3>Aucune suggestion disponible</h3>
                            <p>Nous n'avons pas trouvé d'événements correspondant à vos préférences.</p>
                            <a href="Activités.php" class="btn btn-primary mt-3">Voir tous les événements</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($suggestedEventsDetails as $event): ?>
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="event-card">
                                <div class="event-image">
                                    <img src="../../uploads/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['nom_e']) ?>">
                                </div>
                                <div class="event-details text-center">
                                    <h3 class="event-title"><?= htmlspecialchars($event['nom_e']) ?></h3>
                                    <p class="event-info"><i class="far fa-calendar-alt me-2"></i><?= htmlspecialchars($event['date_e']) ?></p>
                                    <p class="event-info"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($event['lieu_e']) ?></p>
                                    
                                    <div class="action-buttons">
                                        <a href="event-details.php?id=<?= $event['id_e'] ?>" class="btn-view">
                                            <i class="far fa-eye me-1"></i> Voir
                                        </a>
                                        <form method="POST" action="participer.php" style="display: inline;">
                                            <input type="hidden" name="id_e" value="<?= $event['id_e'] ?>">
                                            <button type="submit" class="btn btn-participate">
                                                <i class="fas fa-check me-1"></i> Participer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Suggestions End -->

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