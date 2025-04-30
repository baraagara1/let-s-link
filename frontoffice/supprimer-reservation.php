<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_GET['id'])) {
        header("Location: lister-covoiturages.php");
        exit;
    }

    $id_res = intval($_GET['id']);

    // 1. Récupérer les infos de la réservation
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id_res = ?");
    $stmt->execute([$id_res]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        header("Location: lister-covoiturages.php");
        exit;
    }

    $covoiturage_id = $reservation['covoiturage_id'];
    $nb_places = $reservation['nb_place_res'];

    // 2. Vérifier que le covoiturage existe et récupérer sa date
    $stmt2 = $pdo->prepare("SELECT date FROM covoiturage WHERE id_cov = ?");
    $stmt2->execute([$covoiturage_id]);
    $date_covoiturage = $stmt2->fetchColumn();

    if (!$date_covoiturage) {
        header("Location: lister-covoiturages.php");
        exit;
    }

    // 3. Vérifier si la date est dans moins de 24h
    $dateCov = new DateTime($date_covoiturage . ' 00:00:00');
    $now = new DateTime();
    $diffHeures = ($dateCov->getTimestamp() - $now->getTimestamp()) / 3600;

    if ($diffHeures < 24) {
        header("Location: lister-covoiturages.php?reservation_bloquee=1");
        exit;
    }

    // 4. Supprimer la réservation
    $stmt3 = $pdo->prepare("DELETE FROM reservations WHERE id_res = ?");
    $stmt3->execute([$id_res]);

    // 5. Recréditer les places dans le covoiturage
    $stmt4 = $pdo->prepare("UPDATE covoiturage SET place_dispo = place_dispo + ? WHERE id_cov = ?");
    $stmt4->execute([$nb_places, $covoiturage_id]);

    header("Location: lister-covoiturages.php?reservation_supprimee=1");
    exit;

} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}
?>
