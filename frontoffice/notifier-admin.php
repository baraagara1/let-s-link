<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id_res']) && isset($_GET['id_user'])) {
        $reservationId = intval($_GET['id_res']);
        $utilisateurId = intval($_GET['id_user']);

        // Vérifie si la réservation existe
        $stmtCheck = $pdo->prepare("SELECT id_res FROM reservations WHERE id_res = ?");
        $stmtCheck->execute([$reservationId]);
        if (!$stmtCheck->fetch()) {
            exit("❌ Réservation introuvable.");
        }

        // Vérifie si l'utilisateur existe
        $stmtUser = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE id_utilisateur = ?");
        $stmtUser->execute([$utilisateurId]);
        if (!$stmtUser->fetch()) {
            exit("❌ Utilisateur introuvable.");
        }

        // Vérifie s'il a déjà notifié
        $stmtExist = $pdo->prepare("SELECT * FROM demandes_suppression WHERE reservation_id = ? AND utilisateur_id = ?");
        $stmtExist->execute([$reservationId, $utilisateurId]);
        if ($stmtExist->rowCount() > 0) {
            exit("⚠️ Vous avez déjà envoyé une demande pour cette réservation.");
        }

        // Insertion
        $stmt = $pdo->prepare("INSERT INTO demandes_suppression (reservation_id, utilisateur_id, statut, date_demande) VALUES (?, ?, 'En attente', NOW())");
        $stmt->execute([$reservationId, $utilisateurId]);

        header("Location: lister-covoiturages.php?notification=envoyee");
        exit;
    } else {
        exit("❌ Paramètres manquants (id_res ou id_user).");
    }
} catch (PDOException $e) {
    exit("❌ Erreur : " . $e->getMessage());
}
