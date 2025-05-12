 
<?php
session_start();

// ✅ Connexion PDO (placée avant toute utilisation)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Échec de connexion : " . $e->getMessage());
}

// ✅ Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ✅ ID de l'utilisateur connecté
$idUtilisateurConnecte = $_SESSION['user_id'];
echo "DEBUG ID connecté : " . $idUtilisateurConnecte;

// ✅ Regrouper les signalements par covoiturage
$stmtSignalements = $pdo->prepare("
  SELECT s.*, c.destination
  FROM signalements s
  JOIN covoiturage c ON s.id_cov = c.id_cov
  ORDER BY s.date_signalement DESC
");
$stmtSignalements->execute();
$tousSignalements = $stmtSignalements->fetchAll(PDO::FETCH_ASSOC);

// ➕ Regrouper dans un tableau associatif par id_cov
$signalementsParCov = [];
foreach ($tousSignalements as $sig) {
    $signalementsParCov[$sig['id_cov']][] = $sig;
}



// ✅ Pagination
$parPage = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) $page = 1;
$start = ($page - 1) * $parPage;

// ✅ Compter les covoiturages à venir
$countStmt = $pdo->query("SELECT COUNT(*) FROM covoiturage WHERE date >= CURDATE()");
$totalCovoiturages = (int) $countStmt->fetchColumn();
$totalPages = ceil($totalCovoiturages / $parPage);

// ✅ Récupérer les covoiturages
$stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE date >= CURDATE() ORDER BY date ASC LIMIT :start, :parpage");
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':parpage', $parPage, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Récupérer toutes les réservations
$reservationsStmt = $pdo->query("
    SELECT r.*, c.destination 
    FROM reservations r 
    JOIN covoiturage c ON r.covoiturage_id = c.id_cov
");
$reservations = $reservationsStmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>

<html lang="fr">
<head>

<script>
// Variable globale pour suivre si l'API est chargée
let googleMapsLoaded = false;

function initMap() {
  googleMapsLoaded = true;
  console.log("Google Maps API est prête");
}
</script>

  <meta charset="UTF-8">
  <title>Liste des Covoiturages</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>

/\* Fond sombre flouté \*/
.overlay-flou {
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
backdrop-filter: blur(6px);
background-color: rgba(0, 0, 0, 0.2);
z-index: 998;
}

/\* Formulaire au-dessus du fond flouté \*/
.popup-form {
position: fixed;
top: 140px;
left: 50%;
transform: translateX(-50%);
width: 90%;
max-width: 500px;
max-height: 80vh;
overflow-y: auto;
padding: 40px;
background: white;
border-radius: 12px;
box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
z-index: 999;
animation: fadeIn 0.3s ease;
scrollbar-width: thin;
scrollbar-color: #888 #f1f1f1;

}


.card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
.card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2); }
.card-title i { color: #4e73df; }
.btn-modifier { background-color: #1cc88a; color: white; }
.btn-supprimer { background-color: #e74a3b; color: white; }
.btn-reserver { background-color: #f6c23e; color: white; }
.card-body p i { width: 20px; }


.form-modifier {


background: #fff;
border-radius: 8px;
box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
margin-top: 40px;
overflow: hidden;
}

.form-modifier-header {
background-color: #198754;
color: #fff;
padding: 16px 24px;
font-size: 1.25rem;
font-weight: bold;
display: flex;
align-items: center;
gap: 10px;
}

.form-modifier-body {
padding: 30px;
}

.form-modifier .form-control {
border-radius: 6px;
box-shadow: none;
}

.btn-enregistrer {
background-color: #198754;
color: #fff;
font-weight: bold;
}
body {
background: linear-gradient(135deg, #f1f2f6, #dff9fb);
}

.form-animate {
animation: fadeInUp 0.8s ease-in-out;
}

@keyframes fadeInUp {
from { opacity: 0; transform: translateY(40px); }
to { opacity: 1; transform: translateY(0); }
}

.card-animate {
opacity: 0;
transform: translateY(40px);
animation: fadeInUpCard 0.6s ease forwards;
}

@keyframes fadeInUpCard {
to {
opacity: 1;
transform: translateY(0);
}
}

.pagination .page-link {
border-radius: 8px;
margin: 0 4px;
color: #1b0068;
font-weight: 500;
transition: 0.3s;
}

.pagination .page-item.active .page-link {
background-color: #1b0068;
color: white;
border: none;
}

.pagination .page-link\:hover {
background-color: #e0e0f8;
}


.heure-arrivee-box {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  padding: 8px 15px;
  border-radius: 8px;
  font-weight: 500;
  color: #333;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
..progress-bar {
  transition: width 0.5s ease-in-out;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  justify-content: center;
}



  </style>
</head>
<?php
$editResData = null;
if (isset($_GET['edit_res'])) {
    $idEditRes = intval($_GET['edit_res']);
    $stmtEdit = $pdo->prepare("SELECT * FROM reservations WHERE id_res = ?");
    $stmtEdit->execute([$idEditRes]);
    $editResData = $stmtEdit->fetch(PDO::FETCH_ASSOC);
}
?>

<body>

<?php if ($editResData): ?>

  <style>
    .overlay-flou {
      position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      backdrop-filter: blur(6px); background-color: rgba(0, 0, 0, 0.2);
      z-index: 998;
    }

    .popup-form {
      position: fixed; top: 120px; left: 50%;
      transform: translateX(-50%);
      width: 90%; max-width: 500px;
      background: white; padding: 40px; border-radius: 12px;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.3); z-index: 999;
      animation: fadeIn 0.3s ease;
    }

    .btn-close-custom {
      position: absolute; top: 10px; right: 20px;
      font-size: 1.5rem; color: #333; text-decoration: none;
    }

    .btn-close-custom:hover {
      color: red;
    }
  </style>

  <div class="overlay-flou"></div>

  <div class="popup-form">
  <a href="index.php?action=lister" class="btn-close-custom">&times;</a>
  <h3 class="text-center mb-4"><i class="fas fa-edit me-2"></i>Modifier une réservation</h3>

  <form method="POST" action="index.php?action=traiter_modif_reservation">

  <input type="hidden" name="id_res" value="<?= $editResData['id_res'] ?>">

  <div class="mb-3">
    <label class="form-label">ID Utilisateur</label>
    <input type="text" name="user_id" class="form-control" value="<?= htmlspecialchars($editResData['user_id']) ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Nombre de places</label>
    <input type="number" name="nb_place_res" class="form-control <?= isset($_GET['erreur_places']) ? 'is-invalid' : '' ?>" 
       value="<?= htmlspecialchars($editResData['nb_place_res']) ?>">

<?php if (isset($_GET['erreur_places'])): ?>
  <div class="invalid-feedback">
    ❌ Le nombre de places demandé dépasse la disponibilité.
  </div>
<?php endif; ?>


<?php if (isset($_GET['erreur_places'])): ?>

  <div style="color:red; font-size: 0.9rem; margin-top: 5px;">
    ❌ Le nombre de places demandé dépasse la disponibilité.
  </div>
<?php endif; ?>
      </div>


  <div class="mb-3">
    <label class="form-label">Moyen de paiement</label>
    <select name="moyen_paiement" class="form-select">
      <option <?= $editResData['moyen_paiement'] === 'Espèces' ? 'selected' : '' ?>>Espèces</option>
      <option <?= $editResData['moyen_paiement'] === 'Carte Bancaire' ? 'selected' : '' ?>>Carte Bancaire</option>
      <option <?= $editResData['moyen_paiement'] === 'Virement' ? 'selected' : '' ?>>Virement</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Statut</label>
    <select name="status" class="form-select">
      <option <?= $editResData['status'] === 'En attente' ? 'selected' : '' ?>>En attente</option>
      <option <?= $editResData['status'] === 'Acceptée' ? 'selected' : '' ?>>Acceptée</option>
      <option <?= $editResData['status'] === 'Refusée' ? 'selected' : '' ?>>Refusée</option>
      <option <?= $editResData['status'] === 'Annulée' ? 'selected' : '' ?>>Annulée</option>
    </select>
  </div>

  <div class="text-center mt-4">
    <button type="submit" class="btn btn-success">Enregistrer</button>
  </div>
</form>


  </div>
<?php endif; ?>

<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>

  <div class="alert alert-success text-center mt-3">
    ✅ Covoiturage supprimé avec succès.
  </div>
<?php endif; ?>

<?php if (isset($_GET['reserver'])): ?>

  <?php if (isset($_GET['edit_res'])): 
    $id_res = intval($_GET['edit_res']);
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id_res = ?");
    $stmt->execute([$id_res]);
    $resEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resEdit):
?>

<style>
  .overlay-flou { position: fixed; top: 0; left: 0; width: 100%; height: 100%; backdrop-filter: blur(6px); background-color: rgba(0, 0, 0, 0.2); z-index: 998; }
  .popup-form {
    position: fixed;
    top: 100px;
    left: 50%;
    transform: translateX(-50%);
    width: 90%;
    max-width: 500px;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
    z-index: 999;
    animation: fadeIn 0.3s ease;
  }
  .btn-close-custom {
    position: absolute;
    top: 10px; right: 20px;
    font-size: 1.5rem;
    color: #333;
    text-decoration: none;
    font-weight: bold;
  }
  .btn-close-custom:hover { color: red; }
</style>

<div class="overlay-flou"></div>

<div class="popup-form">
  <a href="index.php?action=lister" class="btn-close-custom">&times;</a>
  <h3 class="text-center mb-4"><i class="fas fa-edit me-2"></i>Modifier la réservation</h3>

  <form method="POST" action="modifier-reservation.php">
    <input type="hidden" name="id_res" value="<?= $resEdit['id_res'] ?>">


<div class="mb-3">
  <label for="user_id" class="form-label">ID Utilisateur</label>
  <input type="text" class="form-control" name="user_id" value="<?= htmlspecialchars($resEdit['user_id']) ?>">
</div>

<div class="mb-3">
  <label for="nb_place_res" class="form-label">Nombre de places</label>
  <input type="text" class="form-control" name="nb_place_res" value="<?= htmlspecialchars($resEdit['nb_place_res']) ?>">
</div>

<div class="mb-3">
  <label for="moyen_paiement" class="form-label">Moyen de paiement</label>
  <select name="moyen_paiement" class="form-select">
    <option value="Espèces" <?= $resEdit['moyen_paiement'] == 'Espèces' ? 'selected' : '' ?>>Espèces</option>
    <option value="Carte Bancaire" <?= $resEdit['moyen_paiement'] == 'Carte Bancaire' ? 'selected' : '' ?>>Carte Bancaire</option>
    <option value="Virement" <?= $resEdit['moyen_paiement'] == 'Virement' ? 'selected' : '' ?>>Virement</option>
  </select>
</div>

<div class="mb-3">
  <label for="status" class="form-label">Statut</label>
  <input type="text" class="form-control" name="status" value="<?= htmlspecialchars($resEdit['status']) ?>">
</div>

<div class="text-center mt-4">
  <button type="submit" class="btn btn-success btn-lg">Enregistrer</button>
</div>
```

  </form>
</div>
<?php endif; endif; ?>

  <style>
  .overlay-flou {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    backdrop-filter: blur(6px);
    background-color: rgba(0, 0, 0, 0.2);
    z-index: 998;
  }

  .popup-form {
    position: fixed;
    top: 120px; /* juste en dessous de la navbar */
    left: 50%;
    transform: translateX(-50%);
    width: 90%;
    max-width: 500px;
    max-height: calc(100vh - 140px); /* évite de dépasser le bas */
    overflow-y: auto;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
    z-index: 999;
    animation: fadeIn 0.3s ease;
  }

  .btn-close-custom {
    position: absolute;
    top: 10px; right: 20px;
    font-size: 1.5rem;
    color: #333;
    text-decoration: none;
    font-weight: bold;
  }

  .btn-close-custom:hover {
    color: red;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateX(-50%) scale(0.95); }
    to { opacity: 1; transform: translateX(-50%) scale(1); }
  }
</style>

<?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'plein'): ?>
  <div class="alert alert-danger text-center">
    ❌ Ce covoiturage est complet ou le nombre de places demandées dépasse la disponibilité.
  </div>
<?php endif; ?>

<div class="overlay-flou"></div>

<div class="popup-form">
  <a href="index.php?action=lister" class="btn-close-custom">&times;</a>
  <h3 class="text-center mb-4"><i class="fas fa-car-side me-2"></i>Réserver un trajet</h3>

  <form method="POST" action="index.php?action=reserver">
    <input type="hidden" name="covoiturage_id" value="<?= intval($_GET['reserver']) ?>">

    <div class="mb-3">
      <label for="user_id" class="form-label">ID Utilisateur</label>
      <input type="text" class="form-control" name="user_id">
    </div>

    <div class="mb-3">
      <label for="nb_place_res" class="form-label">Nombre de places à réserver</label>
      <input type="text" class="form-control" name="nb_place_res" value="1">
    </div>

    <div class="mb-3">
      <label for="moyen_paiement" class="form-label">Moyen de paiement</label>
      <select name="moyen_paiement" class="form-select">
        <option value="">-- Choisir --</option>
        <option value="Espèces">Espèces</option>
        <option value="Carte Bancaire">Carte Bancaire</option>
        <option value="Virement">Virement</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="status" class="form-label">Statut</label>
      <input type="text" class="form-control" name="status" value="En attente">
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-success btn-lg">Valider</button>
    </div>
  </form>
</div>
<?php endif; ?>

<!-- Topbar -->

<div class="container-fluid bg-dark text-light px-0 py-2">
  <div class="row gx-0 d-none d-lg-flex">
    <div class="col-lg-7 px-5 text-start">
      <div class="h-100 d-inline-flex align-items-center me-4"><i class="fa fa-phone-alt me-2"></i>+216 98 999 999</div>
      <div class="h-100 d-inline-flex align-items-center"><i class="far fa-envelope me-2"></i>letslink@gmail.com</div>
    </div>
    <div class="col-lg-5 px-5 text-end">
      <span>Suivez-nous :</span>
      <a class="btn btn-link text-light" href="#"><i class="fab fa-facebook-f"></i></a>
      <a class="btn btn-link text-light" href="#"><i class="fab fa-twitter"></i></a>
      <a class="btn btn-link text-light" href="#"><i class="fab fa-linkedin-in"></i></a>
      <a class="btn btn-link text-light" href="#"><i class="fab fa-instagram"></i></a>
    </div>
  </div>
</div>

<!-- Navbar -->

<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
  <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
  <img src="logo.png" alt="logo" style="width:140px;">
  </a>
  <div class="collapse navbar-collapse" id="navbarCollapse">
    <div class="navbar-nav ms-auto p-4 p-lg-0">
      <a href="index.html" class="nav-item nav-link">Accueil</a>
      <div class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Covoiturage</a>
        <div class="dropdown-menu bg-light m-0">
        <a href="index.php?action=ajouter" class="dropdown-item">Ajouter un covoiturage</a>
<a href="index.php?action=liste" class="dropdown-item">Consulter les covoiturages</a>
        </div>
      </div>
      <a href="contact.html" class="nav-item nav-link">Contact</a>
    </div>
    <a href="#" class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block" style="background-color: #090068; border: none;">
      Se connecter <i class="fa fa-arrow-right ms-3"></i>
    </a>
  </div>
</nav>


<?php if (!empty($signalementsRecus)): ?>
  <div class="alert alert-danger mx-5 mt-4 shadow-sm">
    <h5><i class="fas fa-bell me-2"></i>Vous avez reçu des signalements :</h5>
    <ul class="mb-0">
      <?php foreach ($signalementsRecus as $sos): ?>
        <li>
          <strong><?= htmlspecialchars($sos['destination']) ?> (<?= $sos['date'] ?>)</strong><br>
          <em><?= htmlspecialchars($sos['message']) ?></em><br>
          <small class="text-muted"><?= $sos['date_signalement'] ?></small>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>


<!-- Liste des covoiturages -->

<div class="container py-5 form-animate">

<h1 class="text-center mb-5"><i class="fas fa-list me-2"></i>Liste des covoiturages</h1>


<div class="container my-5">
  <h4>Assistant vocal Let's Link</h4>
  <button onclick="parler()" class="btn btn-primary mb-2">🎙️ Parler</button><br>
  <input type="text" id="question" class="form-control w-50 mb-2" placeholder="Posez une question">
  <button onclick="envoyer()" class="btn btn-success">Envoyer</button>
  <div id="reponse-bot" class="alert alert-info mt-3" style="display: none;"></div>
</div>


<!-- Carte qui s'affichera dynamiquement -->

<div id="map-ma-position" style="height: 300px; width: 100%; display: none;" class="mt-4"></div>

  <div class="row">
  <?php 
$delay = 0; // Effet cascade

foreach ($result as $row):
  $places = (int)$row['place_dispo'];
   

  
?>

<div class="col-md-6 col-lg-4 mb-4">
  <div class="card shadow-sm card-animate" id="cov_<?= $row['id_cov'] ?>" style="animation-delay: <?= $delay ?>s;">



  <!-- Bouton Voir les réservations -->
  <div class="mt-3">
    <button class="btn btn-outline-info btn-sm w-100 toggle-res" data-id="<?= $row['id_cov'] ?>">
      <i class="fas fa-eye me-1"></i> Voir les réservations
    </button>

    <div class="reservations-list mt-3" id="res-list-<?= $row['id_cov'] ?>" style="display:none;">
      <?php
      $hasRes = false;
      foreach ($reservations as $res) {
        if ($res['covoiturage_id'] == $row['id_cov']) {
          $hasRes = true;
          echo '<div class="bg-white border rounded p-3 mb-3 shadow-sm">';


echo '<div class="d-flex justify-content-between align-items-center bg-white border rounded p-2 mb-2 shadow-sm">';
echo '<div class="d-flex align-items-center"><i class="fas fa-user me-2 text-dark"></i>';
echo '<div>';
echo '<div><strong>' . $res["nb_place_res"] . ' place(s)</strong> - ' . htmlspecialchars($res["moyen_paiement"]) . '</div>';

echo '<div><strong>' . $res["nb_place_res"] . ' place(s)</strong> - ' . htmlspecialchars($res["moyen_paiement"]) . '</div>';
echo '</div></div>';

echo '<div class="text-end">';
// Badge statut
echo '<span class="badge bg-info text-white mb-2 d-block">' . htmlspecialchars($res["status"]) . '</span>';

$dateRes = new DateTime($row['date'] . ' 00:00:00');
$now = new DateTime();
$hoursLeft = ($dateRes > $now) ? ($dateRes->getTimestamp() - $now->getTimestamp()) / 3600 : 0;


echo '<div class="d-flex justify-content-end gap-2">';

if ($hoursLeft < 24) {
  echo '<a href="index.php?action=notifier_admin' .
       '&id_res=' . $res['id_res'] .
       '&user_id=' . $res['user_id'] .
       '&nb_places=' . $res['nb_place_res'] .
       '&id_cov=' . $res['covoiturage_id'] . // ✅ ici : id_cov au lieu de covoiturage_id
       '" class="btn btn-danger btn-sm" title="Notifier Admin">';
  echo '<i class="fas fa-bell"></i>';
  echo '</a>';
}
else {
echo '<a href="index.php?action=supprimer_reservation&id=' . $res['id_res'] . '" class="btn btn-outline-danger btn-sm" title="Supprimer">';

echo '<i class="fas fa-trash"></i>';
echo '</a>';
}

// ✅ Bouton Modifier (le crayon) – toujours présent
echo '<a href="index.php?action=lister&edit_res=' . $res['id_res'] . '" class="btn btn-outline-primary btn-sm" title="Modifier">';
echo '<i class="fas fa-pen"></i>';
echo '</a>';

echo '</div>';

echo '</div>'; // Fin boutons
echo '</div>'; // Fin text-end
echo '</div>'; // Fin carte

      }
      }
      if (!$hasRes) {
        echo '<div class="alert alert-light border-start border-4 border-warning mt-3 d-flex align-items-center">';


echo '  <i class="fas fa-info-circle text-warning me-2"></i>';
echo '  <span class="text-secondary">Aucune réservation trouvée pour ce covoiturage.</span>';
echo '</div>';


      }
      ?>
    </div>
  </div>

  <!-- Corps de la carte -->
  <div class="card-body">
  <p class="card-text mb-1"><i class="fas fa-location-arrow"></i> <?= htmlspecialchars($row['lieu_depart']) ?></p>
    <h5 class="card-title"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($row['destination']) ?></h5>
    <p class="card-text mb-1"><i class="fas fa-calendar-alt"></i> <?= htmlspecialchars($row['date']) ?></p>
    <p class="card-text mb-1"><i class="fas fa-clock"></i> <?= htmlspecialchars($row['heure_depart']) ?></p>
    <p class="card-text mb-1"><i class="fas fa-users"></i> <?= $places ?> place<?= $places > 1 ? 's' : '' ?></p>
    <p class="card-text mb-1"><i class="fas fa-money-bill-wave"></i> <?= htmlspecialchars($row['prix_c']) ?> DT</p>
  


    <p class="card-text mb-3"><i class="fas fa-id-badge"></i> ID Utilisateur : <?= htmlspecialchars($row['user_id']) ?></p>
    <?php if ($row['user_id'] == $idUtilisateurConnecte): ?>
  <!-- BOUTONS DU PROPRIÉTAIRE -->
  <a href="index.php?action=lister&edit_cov=<?= $row['id_cov'] ?>" class="btn btn-modifier">
    <i class="fas fa-edit"></i> Modifier 
  </a>

  <a href="#" class="btn btn-supprimer btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalConfirmation" data-id="<?= $row['id_cov'] ?>">
    <i class="fas fa-trash-alt me-1"></i> Supprimer
  </a>

  <?php if ($row['partage_actif'] == 0): ?>
  <form method="POST" action="index.php?action=activer_partage" style="display:inline;">
    <input type="hidden" name="demarrer_partage" value="<?= $row['id_cov'] ?>">
    <button class="btn btn-success btn-sm" type="submit">
      <i class="fa fa-play"></i> Démarrer le partage
    </button>
  </form>
<?php else: ?>
  <button class="btn btn-info btn-sm mt-2"
    onclick="startGeolocation(<?= $row['id_cov'] ?>, <?= $row['user_id'] ?>)">
    <i class="fas fa-map-marker-alt"></i> Voir sur la carte
  </button>
<?php endif; ?>

  <button class="btn btn-outline-danger btn-sm w-100 mt-2 toggle-signalements" data-id="<?= $row['id_cov'] ?>">
    <i class="fas fa-exclamation-triangle me-1"></i> Voir les signalements
  </button>

  <div class="signalements-list mt-2" id="sos-list-<?= $row['id_cov'] ?>" style="display: none;">
    <?php
      $sigs = $signalementsParCov[$row['id_cov']] ?? [];
      if (empty($sigs)) {
        echo '<div class="text-muted">Aucun signalement reçu.</div>';
      } else {
        foreach ($sigs as $s) {
          echo '<div class="border rounded p-2 mb-2 bg-light">';
          echo '<strong>' . htmlspecialchars($s['message']) . '</strong><br>';
          echo '<small class="text-muted">' . $s['date_signalement'] . '</small>';
          echo '</div>';
        }
      }
    ?>
  </div>

<?php else: ?>
 <!-- BOUTONS POUR LES AUTRES UTILISATEURS -->

<?php if ($places <= 0): ?>
  <button class="btn btn-secondary btn-sm" disabled>
    <i class="fas fa-car"></i> Réserver
  </button>
<?php else: ?>
  <a href="index.php?action=lister&reserver=<?= $row['id_cov'] ?>" class="btn btn-warning btn-sm">
    <i class="fas fa-car"></i> Réserver
  </a>
<?php endif; ?>

<button class="btn btn-danger btn-sm me-1"
  onclick="afficherFormulaireSignalement(<?= $row['id_cov'] ?>)">
  <i class="fas fa-exclamation-triangle"></i> SOS
</button>

<!-- 🗺️ Bouton de carte selon le statut du partage -->
<?php if ($row['partage_actif'] == 1): ?>
  <button class="btn btn-info btn-sm mt-2"
    onclick="startGeolocation(<?= $row['id_cov'] ?>, <?= $row['user_id'] ?>)">
    <i class="fas fa-map-marker-alt"></i> Voir sur la carte
  </button>
<?php else: ?>
  <button class="btn btn-secondary btn-sm mt-2" disabled>
    <i class="fas fa-lock"></i> Partage non démarré
  </button>
<?php endif; ?>

<!-- 🆘 Formulaire de signalement -->
<div class="form-signalement mt-3" id="signalement-<?= $row['id_cov'] ?>" style="display: none;">
  <form method="POST" action="index.php?action=ajouter_signalement">
    <input type="hidden" name="id_cov" value="<?= $row['id_cov'] ?>">
    <div class="mb-2">
      <label class="form-label">Message</label>
      <textarea name="message" class="form-control" rows="2" required></textarea>
    </div>
    <div class="d-flex justify-content-between">
      <button type="submit" class="btn btn-danger btn-sm">Envoyer</button>
      <button type="button" class="btn btn-secondary btn-sm" onclick="fermerFormulaireSignalement(<?= $row['id_cov'] ?>)">Annuler</button>
    </div>
  </form>
</div>


<?php endif; ?>





   <!-- Carte cachée + barre durée unique -->
<div id="map-container-<?= $row['id_cov'] ?>" style="display:none; margin-top:15px;">
  <div id="map-<?= $row['id_cov'] ?>" style="height:300px; width:100%;"></div>

 <!-- ✅ Bloc à ajouter autour de la barre -->
 <div id="progress-duree-<?= $row['id_cov'] ?>" style="display:none;">
    <div class="progress mt-2" style="height: 25px; border-radius: 8px;">
      <div id="bar-<?= $row['id_cov'] ?>" class="progress-bar text-white fw-bold"
           role="progressbar" style="width: 0%; background-color: #0d6efd;"
           aria-valuemin="0" aria-valuemax="100">
      </div>
    </div>
  </div>

  <!-- Texte de durée -->
  <div class="text-center mt-1">
    <small id="text-duree-<?= $row['id_cov'] ?>" class="text-muted fst-italic"></small>
  </div>

  <!-- Heure d'arrivée estimée -->
  <div id="heure-arrivee-<?= $row['id_cov'] ?>" class="heure-arrivee-box mt-2" style="display:none;">
    <i class="fas fa-clock me-2 text-primary"></i>
    <span id="heure-texte-<?= $row['id_cov'] ?>"></span>
  </div>

</div>





      





    <!-- Badge dynamique -->
    <?php
      if ($places == 0) {
          echo '<span class="badge bg-danger mt-3 d-block"><i class="fas fa-times-circle me-1 fa-bounce"></i>Complet</span>';
      } elseif ($places <= 2) {
          echo '<span class="badge bg-warning text-dark mt-3 d-block"><i class="fas fa-exclamation-triangle me-1 fa-shake"></i>Presque complet</span>';
      } else {
          echo '<span class="badge bg-success mt-3 d-block"><i class="fas fa-check-circle me-1 fa-fade"></i>Disponible</span>';
      }
    ?>
  </div> <!-- fin card-body -->

</div> <!-- fin card -->


  </div> <!-- fin colonne -->
<?php 
  $delay += 0.15;
endforeach;
?>
<!-- Pagination -->
<nav aria-label="Pagination des covoiturages">
  <ul class="pagination justify-content-center mt-4">



<!-- Précédent avec icône -->
<?php if ($page > 1): ?>
    <li class="page-item">
  <a class="page-link" href="index.php?action=lister&page=<?= $page - 1 ?>" aria-label="Précédent">
    <i class="fas fa-angle-left"></i>
  </a>
</li>

<?php endif; ?>

<!-- Pages numérotées -->
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
  <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
  <a class="page-link" href="index.php?action=lister&page=<?= $i ?>">
  <?= $i ?>
    </a>
  </li>
<?php endfor; ?>

<!-- Suivant avec icône -->
<?php if ($page < $totalPages): ?>
    <li class="page-item">
  <a class="page-link" href="index.php?action=lister&page=<?= $page + 1 ?>" aria-label="Suivant">
    <i class="fas fa-angle-right"></i>
  </a>
</li>

<?php endif; ?>


  </ul>
</nav>

<?php
if (isset($_GET['edit_cov'])) {
  $editId = intval($_GET['edit_cov']);
  $editQuery = $pdo->prepare("SELECT * FROM covoiturage WHERE id_cov = ?");
  $editQuery->execute([$editId]);

  if ($editQuery->rowCount() > 0) {
    $edit = $editQuery->fetch(PDO::FETCH_ASSOC);
?>
<!-- Fond flouté -->
<div class="overlay-flou"></div>

<!-- Popup Modification covoiturage -->
<div class="popup-form">
  <a href="index.php?action=lister" class="btn-close-custom">&times;</a>
  <h3 class="text-center mb-4"><i class="fas fa-pen-to-square me-2"></i>Modifier le covoiturage</h3>

  <form method="POST" action="index.php?action=mettreAJour">
  <input type="hidden" name="id_cov" value="<?= $edit['id_cov'] ?>">

    <div class="mb-3">
      <label class="form-label">Lieu de départ</label>
      <input type="text" name="lieu_depart" class="form-control" value="<?= htmlspecialchars($edit['lieu_depart']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Destination</label>
      <input type="text" name="destination" class="form-control" value="<?= htmlspecialchars($edit['destination']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Date</label>
      <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($edit['date']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Places disponibles</label>
      <input type="text" name="place_dispo" class="form-control" value="<?= htmlspecialchars($edit['place_dispo']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Prix (DT)</label>
      <input type="text" name="prix_c" class="form-control" value="<?= htmlspecialchars($edit['prix_c']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">ID Utilisateur</label>
      <input type="text" name="user_id" class="form-control" value="<?= htmlspecialchars($edit['user_id']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Heure de départ</label>
      <input type="time" name="heure_depart" class="form-control" value="<?= htmlspecialchars($edit['heure_depart']) ?>">
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-success">Enregistrer</button>
    </div>
  </form>
</div>
<?php
  }
}
?>



<!-- Modal de confirmation -->

<div class="modal fade" id="modalConfirmation" tabindex="-1" aria-labelledby="modalConfirmationLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmation</h5>
        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-0">Etes-vous sûr de vouloir supprimer ce covoiturage ?</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <a id="btn-confirm-delete" href="index.php?action=supprimer&id=" class="btn btn-danger">Supprimer</a>

      </div>
    </div>
  </div>
</div>

</div> <!-- ferme le container de la page si besoin -->



<footer class="w-100" style="background-color: #1b0068; color: white; margin: 0; padding: 0;">
  <div class="px-5 py-4" style="width: 100%;">
    <div class="row g-0 m-0">
      <div class="col-md-6 ps-5">
        <h5 class="text-white">Notre Adresse : El Ghazela</h5>
        <p>123 Rue, Ville : tunis, Pays : tunisie</p>
        <p>+216 98 999 999</p>
        <p>letslink@gmail.com</p>
      </div>
      <div class="col-md-6 text-md-end pe-5">
        <h5 class="text-white">Suivez-nous</h5>
        <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-twitter"></i></a>
        <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-linkedin-in"></i></a>
        <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
    <hr class="bg-light m-0">
    <p class="text-center text-light mb-0 py-2">&copy; 2025 Let's Link. Tous droits réservés.</p>
  </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const deleteLinks = document.querySelectorAll('[data-id]');
  const confirmBtn = document.getElementById('btn-confirm-delete');

  deleteLinks.forEach(link => {
    link.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      confirmBtn.setAttribute('href', 'supprimer-covoiturage.php?id=' + id);
    });
  });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.toggle-res').forEach(button => {
    button.addEventListener('click', () => {
      const idCov = button.getAttribute('data-id');
      const container = document.getElementById('res-list-' + idCov);

      // Cacher toutes les autres listes ouvertes
      document.querySelectorAll('.reservations-list').forEach(list => {
        if (list !== container) {
          list.style.display = 'none';
        }
      });

      // Afficher ou cacher celle qu'on a cliquée
      container.style.display = (container.style.display === 'block') ? 'none' : 'block';
    });
  });
});
</script>

<?php if (isset($_GET['reservation_bloquee'])): ?>

<!-- Modal de blocage -->

<div class="modal fade" id="modalBlocage" tabindex="-1" aria-labelledby="blocageLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="blocageLabel">
          <i class="fas fa-ban me-2"></i> Suppression impossible
        </h5>
        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body text-center">
        <p class="fs-5">❌ Vous ne pouvez pas supprimer cette réservation car il reste moins de 24h.</p>
        <p class="text-muted">Veuillez consulter l'administrateur.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- Script pour l'afficher automatiquement -->

<script>
  const modal = new bootstrap.Modal(document.getElementById('modalBlocage'));
  modal.show();
</script>

<?php endif; ?>

<script>
function reserver(id_cov) {
    console.log("🧭 Cliquez sur 'Voir sur la carte' pour ID:", id_cov);

    const mapContainer = document.getElementById('map-container-' + id_cov);
    if (!mapContainer) {
        console.error("❌ map-container introuvable pour ID:", id_cov);
        return;
    }

    // Affiche le conteneur
    mapContainer.style.display = 'block';

    const mapDiv = document.getElementById('map-' + id_cov);
    if (!mapDiv) {
        console.error("❌ Div de carte 'map-' introuvable pour ID:", id_cov);
        return;
    }

    // Récupération de la position
    fetch('index.php?action=get_position&id_cov=' + id_cov)
    .then(response => response.json())
        .then(data => {
            console.log("📍 Données de position reçues:", data);

            if (data.latitude && data.longitude) {
                const lat = parseFloat(data.latitude);
                const lng = parseFloat(data.longitude);

                const map = new google.maps.Map(mapDiv, {
                    zoom: 14,
                    center: { lat: lat, lng: lng }
                });

                const marker = new google.maps.Marker({
                    position: { lat: lat, lng: lng },
                    map: map,
                    title: "Voiture en temps réel",
                    icon: {
                        url: "https://img.icons8.com/color/48/car--v1.png",
                        scaledSize: new google.maps.Size(40, 40)
                    }
                });

                // Mettre à jour la position toutes les 5 secondes
                setInterval(() => {
                    fetch('index.php?action=get_position&id_cov=' + id_cov)
                    .then(res => res.json())
                        .then(update => {
                            console.log("🔄 Mise à jour position:", update);
                            if (update.latitude && update.longitude) {
                                const newPos = {
                                    lat: parseFloat(update.latitude),
                                    lng: parseFloat(update.longitude)
                                };
                                marker.setPosition(newPos);
                                map.panTo(newPos);
                            }
                        });
                }, 5000);
            } else {
                console.warn("⚠️ Données de position incomplètes :", data);
            }
        })
        .catch(error => {
            console.error("❌ Erreur lors de la récupération des coordonnées :", error);
        });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const deleteLinks = document.querySelectorAll('[data-id]');
  const confirmBtn = document.getElementById('btn-confirm-delete');

  deleteLinks.forEach(link => {
    link.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      confirmBtn.setAttribute('href', 'index.php?action=supprimer&id=' + id);

    });
  });

  // Quand on clique sur "Supprimer" dans la modale
  confirmBtn.addEventListener('click', function (e) {
    e.preventDefault();
    window.location.href = this.getAttribute('href');
  });
});
</script>

<script>
// ✅ 1. Initialiser l'API Maps correctement
let map; // Variable globale pour la carte
let marker; // Variable globale pour le marqueur
function initMap() {
  <?php foreach ($result as $row): ?>
    calculerDureeDistance(<?= $row['id_cov'] ?>, <?= json_encode($row['lieu_depart']) ?>);
  <?php endforeach; ?>
}

function startGeolocation(id_cov, user_id) {
  const mapContainer = document.getElementById('map-container-' + id_cov);
  const mapDiv = document.getElementById('map-' + id_cov);
  if (!mapContainer || !mapDiv) return;

  mapContainer.style.display = 'block';

  if (!navigator.geolocation) {
    mapDiv.innerHTML = '<div class="alert alert-warning">Géolocalisation non supportée</div>';
    return;
  }

  let map, marker;

  navigator.geolocation.watchPosition(position => {
    const pos = {
      lat: position.coords.latitude,
      lng: position.coords.longitude
    };

    // 1ère initialisation
    if (!map) {
      map = new google.maps.Map(mapDiv, {
        zoom: 16,
        center: pos
      });

      marker = new google.maps.Marker({
        position: pos,
        map: map,
        title: "Ma position actuelle",
        icon: {
          url: "https://img.icons8.com/color/48/car--v1.png",
          scaledSize: new google.maps.Size(40, 40)
        }
      });
    } else {
      marker.setPosition(pos);
      map.panTo(pos);
    }

    // Envoi au serveur pour mise à jour en base (si conducteur)
    fetch('index.php?action=update_position', {
        method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        id_cov: id_cov,
        latitude: pos.lat,
        longitude: pos.lng
      })
    });
  }, error => {
    mapDiv.innerHTML = '<div class="alert alert-danger">Erreur : ' + error.message + '</div>';
  }, {
    enableHighAccuracy: true,
    maximumAge: 0,
    timeout: 10000
  });
}






</script>

<!-- ✅ 5. Charger l'API Maps avec le bon callback -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9LQbRP6lO29Mv6Etkbn9zci47oak-rtk&callback=initMap&libraries=places" async defer></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const now = new Date();

  <?php foreach ($result as $row): ?>
    <?php if ($row['user_id'] == $idUtilisateurConnecte): ?>
      const dep<?= $row['id_cov'] ?> = new Date("<?= $row['date'] . 'T' . $row['heure_depart'] ?>");

      const diffMin<?= $row['id_cov'] ?> = (dep<?= $row['id_cov'] ?> - now) / 60000;

      if (diffMin > 0 && diffMin <= 15) {
        // 🔔 Notification pour le conducteur
        const alertBox = document.createElement('div');
        alertBox.className = 'alert alert-warning text-center fixed-top m-5 shadow';
        alertBox.style.zIndex = '1050';
        alertBox.innerHTML = `
          <strong>⏰ Attention :</strong> Votre covoiturage "<b><?= htmlspecialchars($row['destination']) ?></b>" commence dans moins de 15 minutes !
        `;
        document.body.appendChild(alertBox);
      }
    <?php endif; ?>
  <?php endforeach; ?>
});
</script>
<script>
function calculerDureeDistance(id_cov, lieu_depart) {
  if (!navigator.geolocation) {
    document.getElementById('duree-' + id_cov).innerText = 'Géolocalisation non supportée';
    return;
  }

  navigator.geolocation.getCurrentPosition(
    function(position) {
      const userLatLng = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };

      const service = new google.maps.DistanceMatrixService();

      service.getDistanceMatrix(
        {
          origins: [userLatLng],
          destinations: [lieu_depart + ', Tunisie'],
          travelMode: google.maps.TravelMode.DRIVING
        },
        function(response, status) {
          if (status === 'OK' && response.rows[0].elements[0].status === 'OK') {

            const element = response.rows[0].elements[0];
            const duree = element.duration.text;
            const distance = element.distance.text;
            const minutes = element.duration.value / 60;
            // Calcule l'heure d'arrivée estimée
const depart = new Date(); // maintenant
depart.setSeconds(depart.getSeconds() + element.duration.value); // ajoute durée

const heures = depart.getHours().toString().padStart(2, '0');
const minutesArrivee = depart.getMinutes().toString().padStart(2, '0');
const heureEstimee = `${heures}:${minutesArrivee}`;

// Affiche et met à jour le texte estimé
document.getElementById('heure-texte-' + id_cov).innerText = `Arrivée estimée : ${heureEstimee}`;
document.getElementById('heure-arrivee-' + id_cov).style.display = 'flex';


document.getElementById('heure-arrivee-' + id_cov).style.display = 'flex';


let texte;
if (minutes < 1.5) {
  texte = '1 minute restante';
} else if (minutes < 60) {
  texte = Math.round(minutes) + ' minutes restantes';
} else {
  const heures = Math.floor(minutes / 60);
  const mins = Math.round(minutes % 60);
  texte = `${heures}h ${mins} min restantes`;
}

document.getElementById('progress-duree-' + id_cov).style.display = 'block';
document.getElementById('text-duree-' + id_cov).innerText = texte;

// Exemple dynamique : réduit la largeur selon le temps restant
let largeur = Math.max(5, Math.min(100, Math.round(100 * (minutes / 120)))); // basé sur max 2h
const bar = document.getElementById('bar-' + id_cov);
bar.style.width = largeur + '%';

// Couleurs dynamiques selon urgence
bar.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'bg-info');
if (minutes < 30) {
  bar.classList.add('bg-danger');
} else if (minutes < 60) {
  bar.classList.add('bg-warning');
} else {
  bar.classList.add('bg-success');
}



          } else {
            console.error("⛔ Erreur Distance Matrix :", response.rows[0].elements[0].status);

            document.getElementById('duree-' + id_cov).innerText = 'Indisponible';
          }
        }
      );
    },
    function(error) {
      document.getElementById('duree-' + id_cov).innerText = 'Position introuvable';
    }
  );
}


</script>

<script>
<?php foreach ($result as $row): ?>
  <?php if ($row['partage_actif'] == 1): ?>
    setInterval(() => {
      calculerDureeDistance(<?= $row['id_cov'] ?>, <?= json_encode($row['lieu_depart']) ?>);
    }, 30000); // toutes les 30 secondes
  <?php endif; ?>
<?php endforeach; ?>
</script>




<script>
function afficherFormulaireSignalement(id) {
  // Cacher tous les formulaires de signalement d'abord
  document.querySelectorAll('.form-signalement').forEach(div => div.style.display = 'none');
  
  // Afficher le bon formulaire
  const form = document.getElementById('signalement-' + id);
  if (form) form.style.display = 'block';
}

function fermerFormulaireSignalement(id) {
  const form = document.getElementById('signalement-' + id);
  if (form) form.style.display = 'none';
}
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.toggle-signalements').forEach(button => {
    button.addEventListener('click', () => {
      const idCov = button.getAttribute('data-id');
      const bloc = document.getElementById('sos-list-' + idCov);
      // Fermer les autres
      document.querySelectorAll('.signalements-list').forEach(el => {
        if (el !== bloc) el.style.display = 'none';
      });
      // Toggle celui-ci
      bloc.style.display = (bloc.style.display === 'block') ? 'none' : 'block';
    });
  });
});
</script>



<script>
function parler() {
  const reco = new webkitSpeechRecognition(); // fonctionne sous Chrome
  reco.lang = "fr-FR";
  reco.onresult = (event) => {
    document.getElementById('question').value = event.results[0][0].transcript;
  };
  reco.start();
}

function envoyer() {
  const question = document.getElementById('question').value;

  fetch("index.php?action=chatbot", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "question=" + encodeURIComponent(question)
  })
  .then(res => res.json())
  .then(data => {
    const div = document.getElementById('reponse-bot');
    div.innerHTML = data.reponse;
    div.style.display = 'block';

    // Lire la réponse à voix haute
    const synth = window.speechSynthesis;
    const utter = new SpeechSynthesisUtterance(data.reponse);
    utter.lang = 'fr-FR';
    synth.speak(utter);

    // Faire défiler vers le covoiturage concerné et le surligner
    if (data.id_cov) {
      const cible = document.getElementById('cov_' + data.id_cov);
      if (cible) {
        cible.scrollIntoView({ behavior: 'smooth', block: 'center' });
        cible.style.border = '3px solid orange';
        cible.style.boxShadow = '0 0 10px orange';
        setTimeout(() => {
          cible.style.border = '';
          cible.style.boxShadow = '';
        }, 3000);
      }
    }
  });
}

</script>




</body>
</html>