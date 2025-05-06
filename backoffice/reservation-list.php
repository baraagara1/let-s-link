<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'covoiturage_db';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 🔥 Récupérer les réservations
    $stmt = $pdo->query("SELECT * FROM reservations ORDER BY id_res DESC");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Compter les demandes de suppression en attente
    $stmtDemandes = $pdo->query("SELECT COUNT(*) FROM demandes_suppression WHERE statut = 'En attente'");
    $nbDemandesSuppression = $stmtDemandes->fetchColumn();
    $editReservation = null;
if (isset($_GET['edit_res'])) {
    $idToEdit = intval($_GET['edit_res']);
    $stmtEdit = $pdo->prepare("SELECT * FROM reservations WHERE id_res = ?");
    $stmtEdit->execute([$idToEdit]);
    $editReservation = $stmtEdit->fetch(PDO::FETCH_ASSOC);
}


} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des Réservations</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">

  <!-- SIDEBAR -->
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
      <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-car"></i></div>
      <div class="sidebar-brand-text mx-3">LET’S LINK</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item"><a class="nav-link" href="index.html"><i class="fas fa-fw fa-tachometer-alt"></i><span>Tableau De Bord</span></a></li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Interface</div>
    <li class="nav-item"><a class="nav-link" href="events.html"><i class="fas fa-fw fa-calendar"></i><span>Gestion des Événements</span></a></li>
    <li class="nav-item"><a class="nav-link" href="users.html"><i class="fas fa-fw fa-users"></i><span>Gestion des Utilisateurs</span></a></li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Extensions</div>
    <li class="nav-item active">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCovoiturage" aria-expanded="true" aria-controls="collapseCovoiturage">
        <i class="fas fa-fw fa-car-side"></i><span>Gestion de Covoiturage</span>
      </a>
      <div id="collapseCovoiturage" class="collapse show" aria-labelledby="headingCovoiturage" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">COVOITURAGE :</h6>
          <a class="collapse-item" href="covoiturage.php">Ajouter un covoiturage</a>
          <a class="collapse-item" href="covoituragelist.php">Consulter les covoiturages</a>
          <a class="collapse-item" href="reservation-list.php">Consulter les reservations</a>



        </div>
      </div>
    </li>
    <li class="nav-item"><a class="nav-link" href="404.html"><i class="fas fa-fw fa-exclamation-triangle"></i><span>404 Page</span></a></li>
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline"><button class="rounded-circle border-0" id="sidebarToggle"></button></div>
  </ul>


  <!-- Content Wrapper -->
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

      <!-- TOPBAR -->
      <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>

        <!-- SEARCH -->
        <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
          <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Recherche..." aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-primary" type="button">
                <i class="fas fa-search fa-sm"></i>
              </button>
            </div>
          </div>
        </form>

        <!-- Cloche de notification -->
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown">
              <i class="fas fa-bell fa-fw"></i>
              <?php if (!empty($nbDemandesSuppression) && $nbDemandesSuppression > 0): ?>
                <span class="badge badge-danger badge-counter"><?php echo $nbDemandesSuppression; ?></span>
              <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
              <h6 class="dropdown-header">Centre d'alertes</h6>
              <?php if (!empty($nbDemandesSuppression) && $nbDemandesSuppression > 0): ?>
                <a class="dropdown-item d-flex align-items-center" href="demandes-suppression.php">
                  <div>
                    <span class="font-weight-bold"><?php echo $nbDemandesSuppression; ?> demande(s) de suppression en attente</span>
                  </div>
                </a>
              <?php else: ?>
                <span class="dropdown-item text-center small text-gray-500">Aucune nouvelle demande</span>
              <?php endif; ?>
            </div>
          </li>
        </ul>
      </nav>

      <!-- MAIN CONTENT -->
      <div class="container-fluid">

      <?php if ($editReservation): ?>
  <div class="modal show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.4); z-index: 1050;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="modif-reservation.php">
          <div class="modal-header">
            <h5 class="modal-title">Modifier la Réservation</h5>
            <a href="reservation-list.php" class="btn-close"></a>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id_res" value="<?= $editReservation['id_res'] ?>">
            
            <div class="mb-3">
              <label class="form-label">ID Utilisateur</label>
              <input type="text" name="utilisateur_id" class="form-control" value="<?= htmlspecialchars($editReservation['utilisateur_id']) ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Nombre de places</label>
              <input type="number" name="nb_place_res" class="form-control" value="<?= htmlspecialchars($editReservation['nb_place_res']) ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Moyen de paiement</label>
              <select name="moyen_paiement" class="form-select">
                <option <?= $editReservation['moyen_paiement'] === 'Espèces' ? 'selected' : '' ?>>Espèces</option>
                <option <?= $editReservation['moyen_paiement'] === 'Carte Bancaire' ? 'selected' : '' ?>>Carte Bancaire</option>
                <option <?= $editReservation['moyen_paiement'] === 'Virement' ? 'selected' : '' ?>>Virement</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Statut</label>
              <select name="status" class="form-select">
                <option <?= $editReservation['status'] === 'En attente' ? 'selected' : '' ?>>En attente</option>
                <option <?= $editReservation['status'] === 'Acceptée' ? 'selected' : '' ?>>Acceptée</option>
                <option <?= $editReservation['status'] === 'Refusée' ? 'selected' : '' ?>>Refusée</option>
                <option <?= $editReservation['status'] === 'Annulée' ? 'selected' : '' ?>>Annulée</option>
              </select>
            </div>

          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Enregistrer</button>
            <a href="reservation-list.php" class="btn btn-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Liste des Réservations</h1>
        </div>

        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Réservations existantes</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>ID Réservation</th>
                    <th>Moyen de Paiement</th>
                    <th>ID Utilisateur</th>
                    <th>ID Covoiturage</th>
                    <th>Statut</th>
                    <th>Nombre de Places</th>
                    <th>Actions</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (count($result) > 0) {
                    foreach ($result as $row) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['id_res']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['moyen_paiement']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['utilisateur_id']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['covoiturage_id']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['nb_place_res']) . "</td>";
                      echo "<td class='text-center'>
                      <a href='reservation-list.php?edit_res=" . $row['id_res'] . "' class='btn btn-sm btn-primary me-1'>
                        <i class='fas fa-edit'></i>
                      </a>
                      <a href='supprimer-reservation.php?id=" . $row['id_res'] . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');\">
                        <i class='fas fa-trash'></i>
                      </a>
                    </td>";
                    

                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='6'>Aucune réservation trouvée.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <footer class="sticky-footer bg-white">
      <div class="container my-auto">
        <div class="copyright text-center my-auto">
          <span>&copy; LET’S LINK 2025</span>
        </div>
      </div>
    </footer>
  </div>
</div>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
