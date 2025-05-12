<?php
session_start();

// 1. Connexion à la base de données
require_once 'config/Database.php';

// 2. Import des modèles
require_once 'models/Covoiturage.php';
require_once 'models/Reservation.php';
require_once 'models/Demande.php'; // ✅ Tu avais oublié le modèle

// 3. Import des contrôleurs
require_once 'controllers/CovoiturageController.php';
require_once 'controllers/ReservationController.php';
require_once 'controllers/DemandesController.php'; // 🟢 replacé ici correctement

// 4. Récupérer l'action demandée
$action = $_GET['action'] ?? 'dashboard';

// 5. Déterminer le bon contrôleur
switch ($action) {
    case 'ajouter':
    case 'traiter_ajout':
    case 'liste':
    case 'lister':
    case 'edit':
    case 'modifier':
    case 'mettreAJour':
    case 'supprimer':
        $controller = new CovoiturageController();
        break;
    case 'exporter_pdf':
            $controller = new CovoiturageController();
            break;
            case 'exporter_reservations_pdf':
                $controller = new ReservationController();
                break;
                


    case 'lister_reservations':
    case 'modifier_reservation':
    case 'supprimer_reservation':
    case 'mettreAJour_reservation':
        $controller = new ReservationController();
        break;

    case 'demandes':
    case 'accepter':
    case 'refuser':
        $controller = new DemandesController(); // ✅ Bien placé ici
        break;

    case 'dashboard':
    case 'accueil':
    default:
        include 'views/home/index.php';
        exit;
}

// 6. Exécuter l'action
switch ($action) {
    case 'ajouter':
        $controller->ajouter();
        break;

    case 'traiter_ajout':
        $controller->traiterAjout();
        break;

    case 'liste':
    case 'lister':
        $controller->lister();
        break;

    case 'edit':
    case 'modifier':
        $controller->modifier();
        break;

    case 'mettreAJour':
        $controller->mettreAJour();
        break;

    case 'supprimer':
        $controller->supprimer();
        break;

        case 'exporter_pdf':
            $controller->exportPdf();
            break;
            
            case 'exporter_reservations_pdf':
                $controller->exportPdf();
                break;
            
    case 'lister_reservations':
        $controller->lister();
        break;

    case 'modifier_reservation':
        $controller->modifier();
        break;

    case 'supprimer_reservation':
        $controller->supprimer();
        break;

    case 'mettreAJour_reservation':
        $controller->mettreAJour_reservation();
        break;

    case 'demandes':
    case 'accepter':
    case 'refuser':
        $controller->handleRequest(); // ✅ Cette ligne est bien exécutée maintenant
        break;
}
