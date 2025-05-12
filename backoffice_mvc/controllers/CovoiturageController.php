<?php
require_once 'models/Covoiturage.php';

class CovoiturageController {
    private $model;

    public function __construct() {
        $this->model = new Covoiturage();
    }

    public function lister() {
        $covoiturages = $this->model->getAll();
        require 'views/covoiturage/liste.php';
    }
    

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'lieu_depart'   => $_POST['lieu_depart'],
                'destination'   => $_POST['destination'],
                'date'          => $_POST['date'],
                'heure_depart'  => $_POST['heure_depart'],
                'place_dispo'   => $_POST['place_dispo'],
                'prix_c'        => $_POST['prix_c'],
                'user_id'=> $_POST['user_id']
            ];

            if ($this->model->ajouter($data)) {
                header('Location: index.php?action=covoiturage_liste');
                exit;
            }
        }

        require 'views/covoiturage/ajouter.php';
    }

    public function modifier() {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'lieu_depart'   => $_POST['lieu_depart'],
                    'destination'   => $_POST['destination'],
                    'date'          => $_POST['date'],
                    'heure_depart'  => $_POST['heure_depart'],
                    'place_dispo'   => $_POST['place_dispo'],
                    'prix_c'        => $_POST['prix_c'],
                    'user_id'=> $_POST['user_id']
                ];

                if ($this->model->modifier($id, $data)) {
                    header('Location: index.php?action=covoiturage_liste');
                    exit;
                }
            }

            $covoiturage = $this->model->getById($id);
            require 'views/covoiturage/modifier.php';
        }
    }

    public function supprimer() {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->model->supprimer($id);
            header('Location: index.php?action=covoiturage_liste');
            exit;
        }
    }

    public function traiterAjout()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];

        // Sécurité : filtrer les champs
        $lieu_depart = trim($_POST['lieu_depart'] ?? '');
        $destination = trim($_POST['destination'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $heure = trim($_POST['heure'] ?? '');
        $places = intval($_POST['place_dispo'] ?? 0);
        $prix = floatval($_POST['prix_c'] ?? 0);
        $user_id = trim($_POST['user_id'] ?? '');

        // Contrôles de validation
        if (empty($lieu_depart)) $errors[] = "Le lieu de départ est requis.";
        if (empty($destination)) $errors[] = "La destination est requise.";
        if (empty($date)) $errors[] = "La date est requise.";
        if ($places <= 0) $errors[] = "Le nombre de places doit être supérieur à 0.";
        if ($prix <= 0) $errors[] = "Le prix doit être supérieur à 0.";
        if (!preg_match('/^\d{6}$/', $user_id)) $errors[] = "ID utilisateur invalide.";

        // En cas d’erreurs
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_values'] = $_POST;
            header('Location: index.php?action=ajouter');
            exit;
        }

        $this->model->ajouter([
            'lieu_depart'   => $lieu_depart,
            'destination'   => $destination,
            'date'          => $date,
            'heure_depart'  => $heure,
            'place_dispo'   => $places,
            'prix_c'        => $prix,
            'user_id'=> $user_id
        ]);
        

        header('Location: index.php?action=lister');
        exit;
    }
}

public function mettreAJour() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id_cov'];

        $data = [
            'lieu_depart'    => $_POST['lieu_depart'],
            'destination'    => $_POST['destination'],
            'date'           => $_POST['date'],
            'heure_depart'   => $_POST['heure_depart'],
            'place_dispo'    => $_POST['place_dispo'],
            'prix_c'         => $_POST['prix_c'],
            'user_id' => $_POST['user_id']
        ];

        $this->model->modifier($id, $data);
        header('Location: index.php?action=lister');
        exit;
    }
}


public function exportPdf() {
    require_once 'fpdf/fpdf.php';

    $covoiturages = $this->model->getAllCovoiturages();

    $pdf = new FPDF();
    $pdf->AddPage();

    // ✅ Logo
    $pdf->Image('img/logo.png', 10, 6, 30); // logo à gauche
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, "Let's Link - Covoiturages", 0, 1, 'C');
    $pdf->Ln(10);

    // ✅ Titre
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(33, 37, 41); // gris foncé
    $pdf->Cell(0, 10, 'Liste des Covoiturages disponibles', 0, 1, 'C');
    $pdf->Ln(5);

    // ✅ En-têtes
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(78, 115, 223); // bleu SB Admin 2
    $pdf->SetTextColor(255);
    $pdf->Cell(30, 10, 'Départ', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Destination', 1, 0, 'C', true);
    $pdf->Cell(22, 10, 'Date', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Heure', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Places', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Prix', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Conducteur', 1, 1, 'C', true);

    // ✅ Contenu
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0);
    foreach ($covoiturages as $cov) {
        $pdf->Cell(30, 10, $cov['lieu_depart'], 1);
        $pdf->Cell(30, 10, $cov['destination'], 1);
        $pdf->Cell(22, 10, $cov['date'], 1);
        $pdf->Cell(20, 10, $cov['heure_depart'], 1);
        $pdf->Cell(20, 10, $cov['place_dispo'], 1);
        $pdf->Cell(20, 10, $cov['prix_c'] . ' DT', 1);
        $pdf->Cell(30, 10, $cov['user_id'], 1);
        $pdf->Ln();
    }

    $pdf->Output('D', 'liste_covoiturages_' . date('Y-m-d') . '.pdf');
    exit;
}



}
