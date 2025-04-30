<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Définir le nombre d'éléments par page
    $parPage = 6;

    // Déterminer la page actuelle
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page <= 0) $page = 1;

    // Calculer le point de départ
    $start = ($page - 1) * $parPage;

    // Compter le nombre total de covoiturages
    $countStmt = $pdo->query("SELECT COUNT(*) FROM covoiturage WHERE date >= CURDATE()");
    $totalCovoiturages = (int) $countStmt->fetchColumn();

    // Calculer le nombre total de pages
    $totalPages = ceil($totalCovoiturages / $parPage);

    // Récupérer les covoiturages pour la page actuelle
    $stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE date >= CURDATE() ORDER BY date ASC LIMIT :start, :parpage");
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':parpage', $parPage, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer toutes les réservations
    $reservationsStmt = $pdo->query("SELECT r.*, c.destination FROM reservations r JOIN covoiturage c ON r.covoiturage_id = c.id_cov");
    $reservations = $reservationsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("❌ Échec de connexion : " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des Covoiturages</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>



/* Fond sombre flouté */
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

/* Formulaire au-dessus du fond flouté */
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

.pagination .page-link:hover {
  background-color: #e0e0f8;
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
    <a href="lister-covoiturages.php" class="btn-close-custom">&times;</a>
    <h3 class="text-center mb-4"><i class="fas fa-edit me-2"></i>Modifier une réservation</h3>

    <form method="POST" action="traiter-modif-reservation.php">
      <input type="hidden" name="id_res" value="<?= $editResData['id_res'] ?>">

      <div class="mb-3">
        <label class="form-label">ID Utilisateur</label>
        <input type="text" name="utilisateur_id" class="form-control" value="<?= htmlspecialchars($editResData['utilisateur_id']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Nombre de places</label>
        <input type="number" name="nb_place_res" class="form-control" value="<?= htmlspecialchars($editResData['nb_place_res']) ?>">
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
  <a href="lister-covoiturages.php" class="btn-close-custom">&times;</a>
  <h3 class="text-center mb-4"><i class="fas fa-edit me-2"></i>Modifier la réservation</h3>

  <form method="POST" action="modifier-reservation.php">
    <input type="hidden" name="id_res" value="<?= $resEdit['id_res'] ?>">

    <div class="mb-3">
      <label for="utilisateur_id" class="form-label">ID Utilisateur</label>
      <input type="text" class="form-control" name="utilisateur_id" value="<?= htmlspecialchars($resEdit['utilisateur_id']) ?>">
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

<div class="overlay-flou"></div>

<div class="popup-form">
  <a href="lister-covoiturages.php" class="btn-close-custom">&times;</a>
  <h3 class="text-center mb-4"><i class="fas fa-car-side me-2"></i>Réserver un trajet</h3>

  <?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'plein'): ?>
    <div class="alert alert-danger text-center">
      ❌ Ce covoiturage est complet ou le nombre de places demandées dépasse la disponibilité.
    </div>
  <?php endif; ?>

  <form method="POST" action="traiter-reservation.php">
    <input type="hidden" name="covoiturage_id" value="<?= intval($_GET['reserver']) ?>">

    <div class="mb-3">
      <label for="utilisateur_id" class="form-label">ID Utilisateur</label>
      <input type="text" class="form-control" name="utilisateur_id">
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
          <a href="ajouter-covoiturage.php" class="dropdown-item">Ajouter un covoiturage</a>
          <a href="lister-covoiturages.php" class="dropdown-item">Consulter les covoiturages</a>
        </div>
      </div>
      <a href="contact.html" class="nav-item nav-link">Contact</a>
    </div>
    <a href="#" class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block" style="background-color: #090068; border: none;">
      Se connecter <i class="fa fa-arrow-right ms-3"></i>
    </a>
  </div>
</nav>







<!-- Liste des covoiturages -->
<div class="container py-5 form-animate">

  
  
<h1 class="text-center mb-5"><i class="fas fa-list me-2"></i>Liste des covoiturages</h1>
  <div class="row">
  <?php 
$delay = 0; // Effet cascade

foreach ($result as $row):
  $places = (int)$row['place_dispo'];
?>
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card shadow-sm card-animate" style="animation-delay: <?= $delay ?>s;">

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
echo '<div><strong>Utilisateur :</strong> ' . htmlspecialchars($res["utilisateur_id"]) . '</div>';
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
    echo '<a href="notifier-admin.php?id_res=' . $res['id_res'] . '&id_user=' . $res['utilisateur_id'] . '" class="btn btn-danger btn-sm" title="Notifier Admin">';
    echo '<i class="fas fa-bell"></i>';
    echo '</a>';
  } else {
    echo '<a href="supprimer-reservation.php?id=' . $res['id_res'] . '" class="btn btn-outline-danger btn-sm" title="Supprimer">';
    echo '<i class="fas fa-trash"></i>';
    echo '</a>';
}


// ✅ Bouton Modifier (le crayon) – toujours présent
echo '<a href="lister-covoiturages.php?edit_res=' . $res['id_res'] . '" class="btn btn-outline-primary btn-sm" title="Modifier">';
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
        <h5 class="card-title"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($row['destination']) ?></h5>
        <p class="card-text mb-1"><i class="fas fa-calendar-alt"></i> <?= htmlspecialchars($row['date']) ?></p>
        <p class="card-text mb-1"><i class="fas fa-users"></i> <?= $places ?> place<?= $places > 1 ? 's' : '' ?></p>
        <p class="card-text mb-1"><i class="fas fa-money-bill-wave"></i> <?= htmlspecialchars($row['prix_c']) ?> DT</p>
        <p class="card-text mb-3"><i class="fas fa-id-badge"></i> ID Utilisateur : <?= htmlspecialchars($row['id_utilisateur']) ?></p>

        <a href="?edit_cov=<?= $row['id_cov'] ?>" class="btn btn-modifier btn-sm me-1">
  <i class="fas fa-edit"></i> Modifier
</a>

        <a href="#" class="btn btn-supprimer btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalConfirmation" data-id="<?= $row['id_cov'] ?>">
          <i class="fas fa-trash-alt me-1"></i> Supprimer
        </a>

        <?php if ($places <= 0): ?>
          <button class="btn btn-secondary btn-sm" disabled>
            <i class="fas fa-car"></i> Réserver
          </button>
        <?php else: ?>
          <a href="lister-covoiturages.php?reserver=<?= $row['id_cov'] ?>" class="btn btn-warning btn-sm me-1">
            <i class="fas fa-car"></i> Réserver
          </a>
          <button class="btn btn-info btn-sm" onclick="reserver(<?= $row['id_cov'] ?>)">
            <i class="fas fa-map-marker-alt"></i> Voir sur la carte
          </button>
        <?php endif; ?>

        <!-- Carte cachée -->
        <div id="map-container-<?= $row['id_cov'] ?>" style="display:none; margin-top:15px;">
          <div id="map-<?= $row['id_cov'] ?>" style="height:300px; width:100%;"></div>
        </div>

        <!-- Démarrer géolocalisation si c'est le propriétaire -->
        <?php if (isset($_SESSION['id_utilisateur']) && $_SESSION['id_utilisateur'] == $row['id_utilisateur']): ?>
          <script>
            startGeolocation(<?= $row['id_cov'] ?>);
          </script>
        <?php endif; ?>

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
        <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Précédent">
          <i class="fas fa-angle-left"></i>
        </a>
      </li>
    <?php endif; ?>

    <!-- Pages numérotées -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?>">
          <?= $i ?>
        </a>
      </li>
    <?php endfor; ?>

    <!-- Suivant avec icône -->
    <?php if ($page < $totalPages): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Suivant">
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
  <a href="lister-covoiturages.php" class="btn-close-custom">&times;</a>
  <h3 class="text-center mb-4"><i class="fas fa-pen-to-square me-2"></i>Modifier le Covoiturage</h3>

  <form method="POST" action="modifier-covoiturage.php" class="row g-3">
    <input type="hidden" name="id_cov" value="<?= $edit['id_cov'] ?>">

    <div class="mb-3">
      <label class="form-label">Destination</label>
      <input type="text" name="destination" class="form-control" value="<?= htmlspecialchars($edit['destination']) ?>">
    </div>

    <!-- Destination -->
<div class="mb-3">
  <label class="form-label">Destination</label>
  <input type="text" name="destination" class="form-control" value="<?= htmlspecialchars($_POST['destination'] ?? $edit['destination']) ?>">
  <?php if (!empty($errors['destination'])): ?>
    <div class="text-danger small mt-1"><?= $errors['destination'] ?></div>
  <?php endif; ?>
</div>

<!-- Date -->
<div class="mb-3">
  <label class="form-label">Date</label>
  <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_POST['date'] ?? $edit['date']) ?>">
  <?php if (!empty($errors['date'])): ?>
    <div class="text-danger small mt-1"><?= $errors['date'] ?></div>
  <?php endif; ?>
</div>

<!-- Place Dispo -->
<div class="mb-3">
  <label class="form-label">Places disponibles</label>
  <input type="text" name="place_dispo" class="form-control" value="<?= htmlspecialchars($_POST['place_dispo'] ?? $edit['place_dispo']) ?>">
  <?php if (!empty($errors['place_dispo'])): ?>
    <div class="text-danger small mt-1"><?= $errors['place_dispo'] ?></div>
  <?php endif; ?>
</div>

<!-- Prix -->
<div class="mb-3">
  <label class="form-label">Prix (DT)</label>
  <input type="text" name="prix_c" class="form-control" value="<?= htmlspecialchars($_POST['prix_c'] ?? $edit['prix_c']) ?>">
  <?php if (!empty($errors['prix_c'])): ?>
    <div class="text-danger small mt-1"><?= $errors['prix_c'] ?></div>
  <?php endif; ?>
</div>

<!-- ID Utilisateur -->
<div class="mb-3">
  <label class="form-label">ID Utilisateur</label>
  <input type="text" name="id_utilisateur" class="form-control" value="<?= htmlspecialchars($_POST['id_utilisateur'] ?? $edit['id_utilisateur']) ?>">
  <?php if (!empty($errors['id_utilisateur'])): ?>
    <div class="text-danger small mt-1"><?= $errors['id_utilisateur'] ?></div>
  <?php endif; ?>
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

</div>

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
        <a id="btn-confirm-delete" href="#" class="btn btn-danger">Supprimer</a>
      </div>
    </div>
  </div>
</div>
</div> <!-- ferme le container de la page si besoin -->

<footer style="background-color: #1b0068; color: white; width: 100%;">
  <div class="container-fluid px-5 py-4">
    <div class="row">
      <div class="col-md-6">
        <h5 class="text-white">Notre Adresse : El Ghazela</h5>
        <p>123 Rue, Ville : tunis, Pays : tunisie</p>
        <p>+216 98 999 999</p>
        <p>letslink@gmail.com</p>
      </div>
      <div class="col-md-6 text-md-end">
        <h5 class="text-white">Suivez-nous</h5>
        <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-twitter"></i></a>
        <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-linkedin-in"></i></a>
        <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
    <hr class="bg-light">
    <p class="text-center text-light mb-0">&copy; 2025 Let's Link. Tous droits réservés.</p>
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
    // Afficher le container de la carte
    const mapContainer = document.getElementById('map-container-' + id_cov);
    mapContainer.style.display = 'block';

    // Initialiser la carte
    fetch('get_position_unique.php?id_cov=' + id_cov)
    .then(response => response.json())
    .then(data => {
        if (data.latitude && data.longitude) {
            var map = new google.maps.Map(document.getElementById('map-' + id_cov), {
                zoom: 14,
                center: {lat: parseFloat(data.latitude), lng: parseFloat(data.longitude)}
            });

            var marker = new google.maps.Marker({
                position: {lat: parseFloat(data.latitude), lng: parseFloat(data.longitude)},
                map: map,
                title: "Position actuelle"
            });

            // Mise à jour de la position toutes les 5 secondes
            setInterval(() => {
                fetch('get_position_unique.php?id_cov=' + id_cov)
                .then(response => response.json())
                .then(update => {
                    if (update.latitude && update.longitude) {
                        var newPos = {lat: parseFloat(update.latitude), lng: parseFloat(update.longitude)};
                        marker.setPosition(newPos);
                        map.panTo(newPos);
                    }
                });
            }, 5000);
        }
    });
}
</script>


/////////////////////////////
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9LQbRP6lO29Mv6Etkbn9zci47oak-rtk&callback=dummyInit" async defer></script>
<script>
function dummyInit() {} // car Google Maps veut toujours un callback
</script>
//////////////////////////////

<script>
function startGeolocation(id_cov) {
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Envoyer latitude et longitude au serveur toutes les 5 secondes
            fetch('update_position.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_cov=${id_cov}&latitude=${latitude}&longitude=${longitude}`
            });
        }, function(error) {
            console.error('Erreur de géolocalisation :', error);
        }, { enableHighAccuracy: true });
    } else {
        alert("Géolocalisation non supportée par votre navigateur.");
    }
}
</script>




</body>
</html>