<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'covoiturage_db';
$user = 'root';
$password = '';

// Connexion PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement de l'action (accepter ou refuser)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id_demande = (int)$_GET['id'];

    // Récupérer la demande
    $stmt = $pdo->prepare("SELECT * FROM demandes_suppression WHERE id = ?");
    $stmt->execute([$id_demande]);
    $demande = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($demande) {
        $reservationId = (int)$demande['reservation_id'];

        if ($action === 'accepter') {
            // Récupérer les infos nécessaires
            $nbPlaces = (int)$demande['nb_places'];
            $idCovoiturage = (int)$demande['covoiturage_id'];
        
            // Supprimer la réservation
            $delete = $pdo->prepare("DELETE FROM reservations WHERE id_res = ?");
            $delete->execute([$reservationId]);
        
            // Incrémenter les places disponibles dans le covoiturage
            $updateCov = $pdo->prepare("UPDATE covoiturage SET place_dispo = place_dispo + ? WHERE id_cov = ?");
            $updateCov->execute([$nbPlaces, $idCovoiturage]);
        
            // Mettre la demande à 'Acceptée'
            $update = $pdo->prepare("UPDATE demandes_suppression SET statut = 'Acceptée' WHERE id = ?");
            $update->execute([$id_demande]);
        
        
        } elseif ($action === 'refuser') {
            // Juste changer le statut à 'Refusée'
            $update = $pdo->prepare("UPDATE demandes_suppression SET statut = 'Refusée' WHERE id = ?");
            $update->execute([$id_demande]);
        }
    }

    // Redirection propre pour recharger la page
    header("Location: demandes-suppression.php");
    exit();
}


// Récupération des demandes
$stmt = $pdo->query("SELECT * FROM demandes_suppression ORDER BY date_demande DESC");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Demandes de Suppression</title>
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
          <a class="collapse-item" href="covoituragelist.php">Consulter la liste</a>
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
      </nav>

      <!-- MAIN CONTENT -->
      <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Demandes de Suppression</h1>
        </div>

        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Demandes en Attente</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>ID Réservation</th>
                    <th>ID Utilisateur</th>
                    <th>Statut</th>
                    <th>Date de Demande</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (count($result) > 0) {
                    foreach ($result as $row) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['reservation_id']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['utilisateur_id']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['statut']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['date_demande']) . "</td>";
                      echo "<td>";
                      if ($row['statut'] == 'En attente') {
                        echo "<a href='demandes-suppression.php?action=accepter&id=" . intval($row['id']) . "' class='btn btn-success btn-sm'>Accepter</a> ";
                        echo "<a href='demandes-suppression.php?action=refuser&id=" . intval($row['id']) . "' class='btn btn-danger btn-sm ml-2'>Refuser</a>";
                      } else {
                        echo "<span class='badge badge-secondary'>Traité</span>";
                      }
                      echo "</td>";
                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='5'>Aucune demande de suppression.</td></tr>";
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
