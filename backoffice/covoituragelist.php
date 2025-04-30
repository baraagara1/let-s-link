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

    $stmt = $pdo->query("SELECT * FROM covoiturage");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Compter les demandes de suppression en attente
$stmtDemandes = $pdo->query("SELECT COUNT(*) FROM demandes_suppression WHERE statut = 'En attente'");
$nbDemandesSuppression = $stmtDemandes->fetchColumn();

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des Covoiturages</title>
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
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>

        <!-- Badge qui affiche le nombre de demandes -->
        <?php if (!empty($nbDemandesSuppression) && $nbDemandesSuppression > 0): ?>
            <span class="badge badge-danger badge-counter"><?php echo $nbDemandesSuppression; ?></span>
        <?php endif; ?>
    </a>

    <!-- Liste déroulante des alertes -->
    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">
            Centre d'alertes
        </h6>

        <?php if (!empty($nbDemandesSuppression) && $nbDemandesSuppression > 0): ?>
            <a class="dropdown-item d-flex align-items-center" href="demandes-suppression.php">
                <div>
                    <div class="small text-gray-500">Nouvelles demandes</div>
                    <span class="font-weight-bold"><?php echo $nbDemandesSuppression; ?> demande(s) de suppression en attente</span>
                </div>
            </a>
        <?php else: ?>
            <span class="dropdown-item text-center small text-gray-500">Aucune nouvelle demande</span>
        <?php endif; ?>
    </div>
</li>

          <!-- Messages -->
          <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown">
              <i class="fas fa-envelope fa-fw"></i>
              <span class="badge badge-danger badge-counter">7</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
              <h6 class="dropdown-header">Centre de messages</h6>
              <a class="dropdown-item text-center small text-gray-500" href="#">Lire plus de messages</a>
            </div>
          </li>
        </ul>
      </nav>

      <!-- MAIN CONTENT -->
      <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Liste des Covoiturages</h1>
          <a href="covoiturage.php" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Ajouter un Covoiturage
          </a>

        </div>

        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Covoiturages disponibles</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>Destination</th>
                    <th>Date</th>
                    <th>Places</th>
                    <th>ID Utilisateur</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (count($result) > 0) {
                    foreach ($result as $row) {
                  
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['place_dispo']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['id_utilisateur']) . "</td>";
                      echo "<td>
                              <a class='btn btn-primary btn-sm' href='modifier-covoiturage.php?id_cov=" . $row['id_cov'] . "'>Modifier</a>
                              <a class='btn btn-danger btn-sm' href='supprimer-covoiturage.php?id_cov=" . $row['id_cov'] . "' onclick='return confirm(\"Voulez-vous vraiment supprimer ce covoiturage ?\")'>Supprimer</a>
                            </td>";
                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='5'>Aucun covoiturage trouvé.</td></tr>";
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

<!-- JS SCRIPTS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>

