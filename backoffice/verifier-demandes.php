<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $demandeId = intval($_GET['id']);

        // Récupérer les infos de la demande (reservation_id, nb_places, covoiturage_id)
        $stmt = $pdo->prepare("SELECT reservation_id, nb_places, covoiturage_id FROM demandes_suppression WHERE id = ?");
        $stmt->execute([$demandeId]);
        $demande = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$demande) {
            exit("❌ Demande introuvable.");
        }

        $idReservation = $demande['reservation_id'];
        $nbPlaces = $demande['nb_places'];
        $idCovoiturage = $demande['covoiturage_id'];

        // Supprimer la réservation
        $stmtDelete = $pdo->prepare("DELETE FROM reservations WHERE id_res = ?");
        $stmtDelete->execute([$idReservation]);

        // Incrémenter le nombre de places disponibles dans le covoiturage
        $stmtUpdate = $pdo->prepare("UPDATE covoiturage SET place_dispo = place_dispo + ? WHERE id_cov = ?");
        $stmtUpdate->execute([$nbPlaces, $idCovoiturage]);

        // Mettre à jour le statut de la demande
        $stmtDemande = $pdo->prepare("UPDATE demandes_suppression SET statut = 'Acceptée' WHERE id = ?");
        $stmtDemande->execute([$demandeId]);

        header("Location: demandes-suppression.php?success=1");
        exit;
    } else {
        exit("❌ ID de la demande manquant.");
    }
} catch (PDOException $e) {
    exit("❌ Erreur : " . $e->getMessage());
}
?>
