<?php session_start(); ?>
<?php
try {
  $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->query("SELECT COUNT(*) FROM demandes_suppression WHERE statut = 'En attente'");
  $nbDemandes = $stmt->fetchColumn();
} catch (PDOException $e) {
  $nbDemandes = 0;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des Covoiturages</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <style>
    td.d-flex.gap-1 > * {
      margin-right: 5px;
    }
  </style>
</head>

<body id="page-top">
<div id="wrapper">
  <!-- SIDEBAR -->
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?action=dashboard">
      <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-car"></i></div>
      <div class="sidebar-brand-text mx-3">LET’S LINK</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item"><a class="nav-link" href="index.php?action=dashboard"><i class="fas fa-fw fa-tachometer-alt"></i><span>Tableau De Bord</span></a></li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Extensions</div>
    <li class="nav-item active">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCovoiturage">
        <i class="fas fa-fw fa-car-side"></i><span>Gestion de Covoiturage</span>
      </a>
      <div id="collapseCovoiturage" class="collapse show">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">COVOITURAGE :</h6>
          <a class="collapse-item" href="index.php?action=ajouter">Ajouter un covoiturage</a>
            <a class="collapse-item" href="index.php?action=lister">Consulter la liste</a>
            <a class="collapse-item" href="index.php?action=lister_reservations">Consulter les reservations</a>
        </div>
      </div>
    </li>
  </ul>

  <!-- CONTENU -->
  <div id="content-wrapper" class="d-flex flex-column">
  <div id="content">

 




<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

  <!-- Barre de recherche alignée à gauche -->
  <form class="form-inline mr-auto ml-md-3 my-2 my-md-0 navbar-search">
    <div class="input-group">
      <input type="text" class="form-control bg-light border-0 small" placeholder="Recherche...">
      <div class="input-group-append">
        <button class="btn btn-primary" type="button">
          <i class="fas fa-search fa-sm"></i>
        </button>
      </div>
    </div>
  </form>

  <!-- 🔔 TON CODE DE LA SONNETTE RESTE INTACT CI-DESSOUS -->
  <ul class="navbar-nav ml-auto">

    <li class="nav-item dropdown no-arrow mx-1">
      <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown">
        <i class="fas fa-bell fa-fw"></i>
        <?php if ($nbDemandes > 0): ?>
          <span class="badge badge-danger badge-counter"><?= $nbDemandes ?></span>
        <?php endif; ?>
      </a>
      <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">Centre d'alertes</h6>
        <?php if ($nbDemandes > 0): ?>
          <a class="dropdown-item d-flex align-items-center" href="index.php?action=demandes">
            <div><span class="font-weight-bold"><?= $nbDemandes ?> demande(s) en attente</span></div>
          </a>
        <?php else: ?>
          <span class="dropdown-item text-center small text-gray-500">Aucune alerte</span>
        <?php endif; ?>
      </div>
    </li>
  </ul>
</nav>

<div class="container-fluid mt-4">


        <div class="card shadow">
          <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Covoiturages disponibles</h6></div>
          <div class="card-body table-responsive">
          
  <div class="table-responsive">
  <table class="table table-bordered table-hover">
  <thead class="thead-light bg-primary text-white">
    <tr>
      <th>Départ</th>
      <th>Destination</th>
      <th>Date</th>
      <th>Heure</th>
      <th>Places</th>
      <th>Prix</th>
      <th>Conducteur</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($covoiturages as $cov): ?>
      <tr>
        <td><?= htmlspecialchars($cov['lieu_depart']) ?></td>
        <td><?= htmlspecialchars($cov['destination']) ?></td>
        <td><?= htmlspecialchars($cov['date']) ?></td>
        <td><?= htmlspecialchars($cov['heure_depart']) ?></td>
        <td><?= htmlspecialchars($cov['place_dispo']) ?></td>
        <td><?= htmlspecialchars($cov['prix_c']) ?> DT</td>
        <td><?= htmlspecialchars($cov['user_id']) ?></td>
        <td class="d-flex justify-content-center gap-1">
          <button class="btn btn-warning btn-sm" onclick='ouvrirModif(<?= json_encode($cov) ?>)' title="Modifier">
            ✏️
          </button>
          <a href="index.php?action=supprimer&id=<?= $cov['id_cov'] ?>" class="btn btn-danger btn-sm" title="Supprimer">
            🗑️
          </a>
          <a href="index.php?action=exporter_pdf&id=<?= $cov['id_cov'] ?>" class="btn btn-danger btn-sm" title="Exporter PDF">
            <i class="fas fa-file-pdf"></i>
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>


          </div>
        </div>
      </div>
    </div>

   <!-- MODAL DE MODIFICATION -->
<div id="overlayModif" style="
  display:none;
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.5); z-index: 9999; backdrop-filter: blur(3px);">
  
  <div style="
    background: white;
    width: 95%;
    max-width: 600px;
    max-height: 90vh;
    margin: 3% auto;
    padding: 30px;
    border-radius: 12px;
    overflow-y: auto;
    position: relative;">
    
    <h4 class="mb-4">✏️ Modifier le covoiturage</h4>
    <form method="POST" action="index.php?action=mettreAJour">
      <input type="hidden" name="id_cov" id="modif_id_cov">

      <div class="form-group"><label>Lieu de départ</label><input class="form-control" name="lieu_depart" id="modif_lieu"></div>
      <div class="form-group"><label>Destination</label><input class="form-control" name="destination" id="modif_dest"></div>
      <div class="form-group"><label>Date</label><input type="date" class="form-control" name="date" id="modif_date"></div>
      <div class="form-group"><label>Heure</label><input type="time" class="form-control" name="heure_depart" id="modif_heure"></div>
      <div class="form-group"><label>Places</label><input class="form-control" name="place_dispo" id="modif_places"></div>
      <div class="form-group"><label>Prix</label><input class="form-control" name="prix_c" id="modif_prix"></div>
      <div class="form-group"><label>ID Conducteur</label><input class="form-control" name="user_id" id="modif_id_user"></div>

      <div class="text-end mt-3">
        <button type="button" class="btn btn-secondary" onclick="fermerModif()">❌ Annuler</button>
        <button type="submit" class="btn btn-success">✅ Enregistrer</button>
      </div>
    </form>
  </div>
</div>


    <!-- FOOTER -->
    <footer class="sticky-footer bg-white">
      <div class="container my-auto text-center"><span>&copy; LET’S LINK 2025</span></div>
    </footer>
  </div>
</div>

<!-- JS -->
<script>
  function ouvrirModif(cov) {
    document.getElementById('modif_id_cov').value = cov.id_cov;
    document.getElementById('modif_lieu').value = cov.lieu_depart;
    document.getElementById('modif_dest').value = cov.destination;
    document.getElementById('modif_date').value = cov.date;
    document.getElementById('modif_heure').value = cov.heure_depart;
    document.getElementById('modif_places').value = cov.place_dispo;
    document.getElementById('modif_prix').value = cov.prix_c;
    document.getElementById('modif_id_user').value = cov.user_id;
    document.getElementById('overlayModif').style.display = 'block';
  }

  function fermerModif() {
    document.getElementById('overlayModif').style.display = 'none';
  }
</script>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>
