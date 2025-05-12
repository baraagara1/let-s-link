<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des réservations</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .bg-gradient-primary { background-color: #4e73df !important; background-image: none; }
  </style>

<style>
  td.d-flex.gap-1 > * {
    margin-right: 5px;
  }
</style>

</head>
<body id="page-top">
  <div id="wrapper">
  <!-- Sidebar -->
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
    <!-- Fin Sidebar -->


    <!-- Contenu principal -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <form class="form-inline mr-auto ml-md-3">
            <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" placeholder="Recherche...">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button"><i class="fas fa-search fa-sm"></i></button>
              </div>
            </div>
          </form>
        </nav>

        <div class="container-fluid">
          <h1 class="h3 mb-4 text-gray-800">Liste des réservations</h1>
          <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Réservations disponibles</h6></div>
            <div class="card-body">
          

              <div class="table-responsive">
              <table class="table table-bordered" width="100%" cellspacing="0">
  <thead class="thead-light bg-primary text-white">
    <tr>
      <th>ID</th>
      <th>Moyen de paiement</th>
      <th>Utilisateur ID</th>
      <th>Covoiturage ID</th>
      <th>Statut</th>
      <th>Places réservées</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($reservations as $res): ?>
      <tr>
        <td><?= htmlspecialchars($res['id_res']) ?></td>
        <td><?= htmlspecialchars($res['moyen_paiement']) ?></td>
        <td><?= htmlspecialchars($res['user_id']) ?></td>
        <td><?= htmlspecialchars($res['covoiturage_id']) ?></td>
        <td><span class="badge badge-info"><?= htmlspecialchars($res['status']) ?></span></td>
        <td><?= htmlspecialchars($res['nb_place_res']) ?></td>
        <td class="d-flex justify-content-center gap-1">
          <button class="btn btn-warning btn-sm"
            onclick="ouvrirReservationModif(
              '<?= $res['id_res'] ?>',
              '<?= htmlspecialchars($res['moyen_paiement']) ?>',
              '<?= htmlspecialchars($res['user_id']) ?>',
              '<?= htmlspecialchars($res['covoiturage_id']) ?>',
              '<?= htmlspecialchars($res['status']) ?>',
              '<?= htmlspecialchars($res['nb_place_res']) ?>'
            )" title="Modifier">
            ✏️
          </button>
          <a href="index.php?action=supprimer_reservation&id=<?= $res['id_res'] ?>" class="btn btn-danger btn-sm" title="Supprimer" onclick="return confirm('Supprimer cette réservation ?')">
            🗑️
          </a>
          <a href="index.php?action=exporter_reservations_pdf&id=<?= $res['id_res'] ?>" class="btn btn-danger btn-sm" title="Exporter PDF">
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
      </div>
      <footer class="sticky-footer bg-white">
        <div class="container my-auto text-center">
          <span>&copy; LET'S LINK 2025</span>
        </div>
      </footer>
    </div>
  </div>


  <!-- ✅ MODAL DE MODIFICATION RÉSERVATION -->
<div id="overlayReservationModif" style="display:none;
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.5); z-index: 9999; backdrop-filter: blur(3px);">
  <div style="background: white; width: 600px; margin: 5% auto; padding: 30px; border-radius: 10px; position: relative;">
    <h4 class="mb-4">✏️ Modifier la réservation</h4>
    <form method="POST" action="index.php?action=mettreAJour_reservation">
      <input type="hidden" name="id_res" id="modif_id_res">

      <div class="form-group"><label>Moyen de paiement</label>
        <input class="form-control" name="moyen_paiement" id="modif_moyen">
      </div>
      <div class="form-group"><label>ID Utilisateur</label>
        <input class="form-control" name="user_id" id="modif_utilisateur">
      </div>
      <div class="form-group"><label>ID Covoiturage</label>
        <input class="form-control" name="covoiturage_id" id="modif_covoiturage">
      </div>
      <div class="form-group"><label>Statut</label>
        <select class="form-control" name="status" id="modif_statut">
          <option>En attente</option>
          <option>Acceptée</option>
          <option>Refusée</option>
          <option>Annulée</option>
        </select>
      </div>
      <div class="form-group"><label>Places réservées</label>
        <input class="form-control" name="nb_place_res" id="modif_places">
      </div>

      <div class="text-right">
        <button type="button" class="btn btn-secondary" onclick="fermerReservationModif()">❌ Annuler</button>
        <button type="submit" class="btn btn-success">✅ Enregistrer</button>
      </div>
    </form>
  </div>
</div>


  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>


  <script>
function ouvrirReservationModif(id, moyen, userId, covId, statut, places) {
  document.getElementById('modif_id_res').value = id;
  document.getElementById('modif_moyen').value = moyen;
  document.getElementById('modif_utilisateur').value = userId;
  document.getElementById('modif_covoiturage').value = covId;
  document.getElementById('modif_statut').value = statut;
  document.getElementById('modif_places').value = places;
  document.getElementById('overlayReservationModif').style.display = 'block';
}

function fermerReservationModif() {
  document.getElementById('overlayReservationModif').style.display = 'none';
}
</script>

</body>
</html>
