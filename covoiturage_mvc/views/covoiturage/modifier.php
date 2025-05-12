<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("ID invalide.");
}

$id = intval($_GET['id']);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE id_cov = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $covoiturage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$covoiturage) {
        exit("Covoiturage introuvable ou non autorisé.");
    }
} catch (PDOException $e) {
    die("Erreur PDO : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Covoiturage</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Modifier le covoiturage</h2>
    <form method="POST" action="/controllers/covoiturage/traiter_modif.php">
        <input type="hidden" name="id_cov" value="<?= htmlspecialchars($covoiturage['id_cov']) ?>">

        <div class="mb-3">
            <label for="lieu_depart" class="form-label">Lieu de départ</label>
            <input type="text" name="lieu_depart" class="form-control" value="<?= htmlspecialchars($covoiturage['lieu_depart']) ?>">
        </div>

        <div class="mb-3">
            <label for="destination" class="form-label">Destination</label>
            <input type="text" name="destination" class="form-control" value="<?= htmlspecialchars($covoiturage['destination']) ?>">
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($covoiturage['date']) ?>">
        </div>

        <div class="mb-3">
            <label for="place_dispo" class="form-label">Places disponibles</label>
            <input type="text" name="place_dispo" class="form-control" value="<?= htmlspecialchars($covoiturage['place_dispo']) ?>">
        </div>

        <div class="mb-3">
            <label for="prix_c" class="form-label">Prix (DT)</label>
            <input type="text" name="prix_c" class="form-control" value="<?= htmlspecialchars($covoiturage['prix_c']) ?>">
        </div>

        <div class="mb-3">
            <label for="heure_depart" class="form-label">Heure de départ</label>
            <input type="time" name="heure_depart" class="form-control" value="<?= htmlspecialchars($covoiturage['heure_depart']) ?>">
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success">Enregistrer</button>
        </div>
    </form>
</div>
</body>
</html>
