<?php
// Demandes.php

// 1) Connexion à la base de données avec PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// 2) Traitement des actions (Accepter, Refuser, En attente)
if (isset($_GET['action'], $_GET['id_demande'])) {
    $id_demande = intval($_GET['id_demande']);
    $action = $_GET['action'];
    if ($action === 'accepter') {
        $conn->query("UPDATE demandes SET statut='Acceptée' WHERE idDemande = $id_demande");
    } elseif ($action === 'refuser') {
        $conn->query("UPDATE demandes SET statut='Refusée' WHERE idDemande = $id_demande");
    } elseif ($action === 'attente') {
        $conn->query("UPDATE demandes SET statut='En attente' WHERE idDemande = $id_demande");
    }
    header("Location: Demandes.php" . (isset($_GET['search']) ? "?search=".urlencode($_GET['search']) : ""));
    exit;
}

// 3) Recherche (via GET)
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// 4) Requête pour récupérer les demandes
$sql = "
    SELECT idDemande, nom, type, offre, statut, date_demande
    FROM demandes
    WHERE nom      LIKE :search
       OR type     LIKE :search
       OR offre    LIKE :search
    ORDER BY date_demande DESC
";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', "%{$search}%", PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des Demandes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- SB Admin 2 & FontAwesome -->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .search-box { margin-bottom: 1rem; }
    table { background: #fff; }
    th, td { vertical-align: middle; text-align: center; }
  </style>
</head>
<body id="page-top">
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../index.php">
        <div class="sidebar-brand-icon"><i class="fas fa-handshake"></i></div>
        <div class="sidebar-brand-text mx-3">Let's Link</div>
      </a>
      <hr class="sidebar-divider my-0">

      <!-- Tableau de bord -->
      <li class="nav-item">
        <a class="nav-link" href="../index.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Tableau de bord</span>
        </a>
      </li>
      <hr class="sidebar-divider">

      <!-- Gestion -->
      <div class="sidebar-heading">Gestion</div>

      <!-- Partenaires collapse -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePartenaires"
           aria-expanded="true" aria-controls="collapsePartenaires">
          <i class="fas fa-fw fa-handshake"></i>
          <span>Partenaires</span>
        </a>
        <div id="collapsePartenaires" class="collapse" aria-labelledby="headingPartenaires"
             data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Gestion des Partenaires :</h6>
            <a class="collapse-item" href="liste.php">Liste des partenaires</a>
            <a class="collapse-item active" href="Demandes.php">Demandes</a>
          </div>
        </div>
      </li>

      <hr class="sidebar-divider d-none d-md-block">
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content" class="pt-4">
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Liste des Demandes</h1>

          <!-- Search Box -->
          <div class="search-box">
            <input type="text"
                   id="search"
                   class="form-control"
                   placeholder="Rechercher..."
                   value="<?= htmlspecialchars($search) ?>">
          </div>

          <!-- Tableau des demandes -->
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="thead-light">
                <tr>
                  <th>Partenaire</th>
                  <th>Offre</th>
                  <th>Statut</th>
                  <th>Date de demande</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($results)): ?>
                  <tr><td colspan="5">Aucune demande trouvée.</td></tr>
                <?php else: ?>
                  <?php foreach ($results as $row): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['nom']) ?></td>
                      <td><?= htmlspecialchars($row['offre']) ?></td>
                      <td><?= htmlspecialchars($row['statut']) ?></td>
                      <td><?= htmlspecialchars($row['date_demande']) ?></td>
                      <td>
                        <a href="?action=accepter&amp;id_demande=<?= $row['idDemande'] ?>&amp;<?= $search!==''? 'search='.urlencode($search):'' ?>"
                           class="btn btn-success btn-sm">Accepter</a>
                        <a href="?action=refuser&amp;id_demande=<?= $row['idDemande'] ?>&amp;<?= $search!==''? 'search='.urlencode($search):'' ?>"
                           class="btn btn-danger btn-sm">Refuser</a>
                        <a href="?action=attente&amp;id_demande=<?= $row['idDemande'] ?>&amp;<?= $search!==''? 'search='.urlencode($search):'' ?>"
                           class="btn btn-warning btn-sm">En attente</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scripts -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="../js/sb-admin-2.min.js"></script>
  <script>
    // Recherche live : met à jour le paramètre ?search=
    document.getElementById('search').addEventListener('input', function() {
      const q = encodeURIComponent(this.value);
      window.location.href = 'Demandes.php' + (q ? '?search=' + q : '');
    });
  </script>
</body>
</html>
