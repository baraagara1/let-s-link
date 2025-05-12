<?php
require_once 'models/Demande.php';

class DemandesController {
    private $model;

    public function __construct() {
        $this->model = new Demande();
    }

    public function handleRequest() {
        if (isset($_GET['action'], $_GET['id']) && in_array($_GET['action'], ['accepter', 'refuser'])) {
            $id = intval($_GET['id']);
            $this->model->changerStatut($id, $_GET['action']); // ✅ Correction ici
            header('Location: index.php?action=demandes');
            exit;
        }
    
        $demandes = $this->model->getAll();
        include 'views/demandes/liste.php';
    }
    
    }
