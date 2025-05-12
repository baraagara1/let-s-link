<?php
require_once 'models/Reservation.php';

class ReservationController {
    private $model;

    public function __construct() {
        $this->model = new Reservation();
    }

    // Liste toutes les réservations
    public function lister() {
        $reservations = $this->model->getAll();
        include 'views/reservation/liste.php';
    }

    // Supprime une réservation
    public function supprimer() {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->model->supprimer($id);
            header('Location: index.php?action=lister_reservations');
            exit;
        }
    }

    // Modifie une réservation
    public function modifier() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_res'];
            $data = [
                'id_cov'           => $_POST['id_cov'],
                'user_id'   => $_POST['user_id'],
                'statut'           => $_POST['statut'],
                'date_reservation' => $_POST['date_reservation']
            ];
            $this->model->modifier($id, $data);
            header('Location: index.php?action=lister_reservations');
            exit;
        }
    }

    public function mettreAJour_reservation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_res'];
            $data = [
                'moyen_paiement'   => $_POST['moyen_paiement'],
                'user_id'   => $_POST['user_id'],
                'covoiturage_id'   => $_POST['covoiturage_id'],
                'status'           => $_POST['status'],
                'nb_place_res'     => $_POST['nb_place_res']
            ];
            $this->model->modifier($id, $data);
            header('Location: index.php?action=lister_reservations');
            exit;
        }
    }


    public function exportPdf() {
        require_once 'fpdf/fpdf.php';
    
        $reservations = $this->model->getAllReservations(); // méthode à créer juste après
    
        $pdf = new FPDF();
        $pdf->AddPage();
    
        // ✅ Logo
        $pdf->Image('img/logo.png', 10, 6, 30);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, "Let's Link - Réservations", 0, 1, 'C');
        $pdf->Ln(10);
    
        // ✅ Titre
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->Cell(0, 10, 'Liste des Réservations', 0, 1, 'C');
        $pdf->Ln(5);
    
        // ✅ En-têtes
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(78, 115, 223);
        $pdf->SetTextColor(255);
        $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell(35, 10, 'Paiement', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Utilisateur', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Covoiturage', 1, 0, 'C', true);
        $pdf->Cell(35, 10, 'Statut', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Places', 1, 1, 'C', true);
    
        // ✅ Contenu
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0);
        foreach ($reservations as $res) {
            $pdf->Cell(20, 10, $res['id_res'], 1);
            $pdf->Cell(35, 10, $res['moyen_paiement'], 1);
            $pdf->Cell(30, 10, $res['user_id'], 1);
            $pdf->Cell(30, 10, $res['covoiturage_id'], 1);
            $pdf->Cell(35, 10, $res['status'], 1);
            $pdf->Cell(30, 10, $res['nb_place_res'], 1);
            $pdf->Ln();
        }
    
        $pdf->Output('D', 'liste_reservations_' . date('Y-m-d') . '.pdf');
        exit;
    }
    
    
}
