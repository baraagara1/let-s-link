<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_res = intval($_POST['id_res']);
        $utilisateur_id = intval($_POST['utilisateur_id']);
        $nb_place_res = intval($_POST['nb_place_res']);
        $moyen_paiement = $_POST['moyen_paiement'];
        $status = $_POST['status'];

        // Contrôle de saisie
        if (strlen($utilisateur_id) !== 6 || !ctype_digit((string)$utilisateur_id)) {
            header("Location: lister-covoiturages.php?edit_res=$id_res&erreur=utilisateur");
            exit;
        }

        if ($nb_place_res < 1) {
            header("Location: lister-covoiturages.php?edit_res=$id_res&erreur_places=1");
            exit;
        }

        if (!in_array($moyen_paiement, ['Espèces', 'Carte Bancaire', 'Virement'])) {
            header("Location: lister-covoiturages.php?edit_res=$id_res&erreur=paiement");
            exit;
        }

        if (!in_array($status, ['En attente', 'Acceptée', 'Refusée', 'Annulée'])) {
            header("Location: lister-covoiturages.php?edit_res=$id_res&erreur=statut");
            exit;
        }

        // Récupérer ancienne réservation et places dispo
        $stmtOld = $pdo->prepare("SELECT r.nb_place_res AS ancien_nb, r.covoiturage_id, c.place_dispo
                                  FROM reservations r
                                  JOIN covoiturage c ON r.covoiturage_id = c.id_cov
                                  WHERE r.id_res = ?");
        $stmtOld->execute([$id_res]);
        $data = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            exit("❌ Réservation ou covoiturage introuvable.");
        }

        $ancien_nb = intval($data['ancien_nb']);
        $id_cov = intval($data['covoiturage_id']);
        $place_dispo = intval($data['place_dispo']);

        $diff = $nb_place_res - $ancien_nb;

        // Si on augmente le nombre de places
        if ($diff > 0 && $diff > $place_dispo) {
            header("Location: lister-covoiturages.php?edit_res=$id_res&erreur_places=1");
            exit;
        }

        // Mise à jour de la réservation
        $stmtUpdate = $pdo->prepare("UPDATE reservations SET
            utilisateur_id = ?, nb_place_res = ?, moyen_paiement = ?, status = ?
            WHERE id_res = ?");
        $stmtUpdate->execute([$utilisateur_id, $nb_place_res, $moyen_paiement, $status, $id_res]);

        // Mise à jour du nombre de places dans le covoiturage
        $stmtCov = $pdo->prepare("UPDATE covoiturage SET place_dispo = place_dispo - ? WHERE id_cov = ?");
        $stmtCov->execute([$diff, $id_cov]);

        header("Location: lister-covoiturages.php?modif=ok");
        exit;
    } else {
        exit("Méthode invalide.");
    }
} catch (PDOException $e) {
    exit("❌ Erreur : " . $e->getMessage());
}
