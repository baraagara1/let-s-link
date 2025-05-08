<?php
require_once '../../../controller/PostController.php';
require_once '../../../TCPDF-main/TCPDF-main/tcpdf.php';

$controller = new PostController();
$posts = $controller->getAllPosts();
$posts = $posts->fetchAll(PDO::FETCH_ASSOC); // tableau associatif

// Initialiser le PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Liste des Posts');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Titre principal
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetTextColor(6, 187, 204);
$pdf->Cell(0, 15, 'Liste des Posts du Blog', 0, 1, 'C');
$pdf->Ln(5);

// Largeurs colonnes : #, Titre, Contenu, Image
$w = [10, 50, 80, 40];

// En-tête du tableau
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(6, 187, 204); // bleu clair
$pdf->SetTextColor(255); // texte blanc
$pdf->Cell($w[0], 10, '#', 1, 0, 'C', true);
$pdf->Cell($w[1], 10, 'Titre', 1, 0, 'C', true);
$pdf->Cell($w[2], 10, 'Contenu', 1, 0, 'C', true);
$pdf->Cell($w[3], 10, 'Image', 1, 1, 'C', true);

// Réinitialiser style pour le contenu
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0); // noir

foreach ($posts as $index => $post) {
    $fill = ($index % 2 == 0); // alternance
    $pdf->SetFillColor($fill ? 245 : 255, 245, 245); // gris clair ou blanc

    // Cellule #
    $pdf->Cell($w[0], 30, $index + 1, 1, 0, 'C', true);
    // Cellule titre
    $pdf->MultiCell($w[1], 30, $post['titre'], 1, 'L', true, 0);
    // Cellule contenu
    $pdf->MultiCell($w[2], 30, $post['text'], 1, 'L', true, 0);

    // Cellule image
    $imagePath = '../../../uploads/' . $post['jointure'];
    if (!empty($post['jointure'])) {
    $imagePath = realpath('../../../uploads/' . $post['jointure']);
    if (!$imagePath || !file_exists($imagePath)) {
        $pdf->Cell($w[3], 30, 'VIDEO', 1, 0, 'C', true);
    } else {
        $pdf->Cell($w[3], 30, '', 1, 0, 'C', true);
        $x = $pdf->GetX() - $w[3];
        $y = $pdf->GetY();

        // DEBUG : nom du fichier dans le PDF
        $pdf->SetXY($x, $y + 20);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell($w[3], 5, basename($imagePath), 0, 0, 'C');

        // Image (test avec JPEG/PNG uniquement)
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $pdf->Image($imagePath, $x + 5, $y + 5, 30, 20, '', '', '', false, 300, '', false, false, 0);
        }
    }
} else {
    $pdf->Cell($w[3], 30, 'Aucune', 1, 0, 'C', true);
}


    $pdf->Ln();
}

$pdf->Output('liste_posts_tableau.pdf', 'I');
