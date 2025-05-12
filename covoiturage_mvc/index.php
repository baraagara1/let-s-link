<?php

// 🔁 Récupère l'action passée dans l'URL
$action = $_GET['action'] ?? 'accueil';

// ✨ Redirection vers la bonne page selon l'action
switch ($action) {
    case 'about':
        require_once 'controllers/AboutController.php';
        $controller = new AboutController();
        $controller->index();
        break;
    

    case 'accueil':
        require_once 'controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;

        case 'lister':
            require_once 'controllers/CovoiturageController.php';
            $controller = new CovoiturageController();
            $controller->index();
            break;

        case 'enregistrer':
                require_once 'controllers/CovoiturageController.php';
                $controller = new CovoiturageController();
                $controller->enregistrer();  // méthode POST qui ajoute dans la BDD
                break;
        

    case 'ajouter':
        require_once 'controllers/CovoiturageController.php';
        $controller = new CovoiturageController();
        $controller->ajouter();
        break;

    case 'reserver':
        require_once 'controllers/ReservationController.php';
        $controller = new ReservationController();
        $controller->reserver();
        break;

    // 👇 Ajoute d'autres actions ici si besoin
    case 'login':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;


        case 'supprimer':
            require_once 'controllers/CovoiturageController.php';
            $controller = new CovoiturageController();
            $controller->supprimer();
            break;

        case 'mettreAJour':
                require_once 'controllers/CovoiturageController.php';
                $controller = new CovoiturageController();
                $controller->mettreAJour();
                break;

        case 'traiter_modif_reservation':
                    require_once 'controllers/ReservationController.php';
                    $controller = new ReservationController();
                    $controller->traiterModification(); // 👈 méthode à coder
                    break;
                
            
        case 'notifier_admin':
                        require_once 'controllers/ReservationController.php';
                        $controller = new ReservationController();
                        $controller->notifierAdmin();
                        break;

        case 'supprimer_reservation':
                            require_once 'controllers/ReservationController.php';
                            $controller = new ReservationController();
                            $controller->supprimer();
                            break;
                        
                     
            
        case 'update_position':
                                require_once 'controllers/GeolocalisationController.php';
                                $controller = new GeolocalisationController();
                                $controller->updatePosition();
                                break;
                            
        case 'get_position':
                                require_once 'controllers/GeolocalisationController.php';
                                $controller = new GeolocalisationController();
                                $controller->getPosition();
                                break;
                            
        case 'activer_partage':
                                require_once 'controllers/GeolocalisationController.php';
                                $controller = new GeolocalisationController();
                                $controller->activerPartage();
                                break;


        case 'chatbot':
                                    require_once 'controllers/ChatbotController.php';
                                    $controller = new ChatbotController();
                                    $controller->repondre();
                                    break;
                                
                                         
         case 'ajouter_signalement':
                                    require_once 'controllers/SignalementController.php';
                                    $controller = new SignalementController();
                                    $controller->ajouter();
                                    break;
                                    

    default:
        echo "❌ Erreur 404 : action inconnue.";
}
