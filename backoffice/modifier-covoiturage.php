<?php
$host = 'localhost';
$dbname = 'covoiturage_db';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if (!isset($_GET['id_cov'])) {
    echo "ID du covoiturage manquant.";
    exit();
}

$id = intval($_GET['id_cov']);
$sql = "SELECT * FROM covoiturage WHERE id_cov = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);

if ($stmt->rowCount() != 1) {
    echo "Covoiturage introuvable.";
    exit();
}

$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Covoiturage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

<!-- Wrapper -->
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
    <li class="nav-item active"><a class="nav-link" href="covoituragelist.php"><i class="fas fa-fw fa-car-side"></i><span>Gestion de Covoiturage</span></a></li>
    <li class="nav-item"><a class="nav-link" href="404.html"><i class="fas fa-fw fa-exclamation-triangle"></i><span>404 Page</span></a></li>
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline"><button class="rounded-circle border-0" id="sidebarToggle"></button></div>
  </ul>

  <!-- Content Wrapper -->
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
      <!-- Topbar -->
      <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
      </nav>

      <!-- Main Content -->
      <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Modifier un Covoiturage</h1>

        <form action="traitement-modif.php" method="POST">
          <input type="hidden" name="id_cov" value="<?= $row['id_cov']; ?>">

          <div class="form-group">
              <label>Destination</label>
              <input type="text" name="destination" class="form-control" value="<?= htmlspecialchars($row['destination']); ?>" required>
          </div>

          <div class="form-group">
              <label>Date</label>
              <input type="date" name="date" class="form-control" value="<?= $row['date']; ?>" required>
          </div>

          <div class="form-group">
              <label>Places disponibles</label>
              <input type="number" name="place_dispo" class="form-control" value="<?= $row['place_dispo']; ?>" required>
          </div>

          <div class="form-group">
              <label>ID de l'utilisateur</label>
              <input type="number" name="id_utilisateur" class="form-control" value="<?= $row['id_utilisateur']; ?>" required>
          </div>

          <button type="submit" class="btn btn-primary">Enregistrer</button>
          <a href="covoituragelist.php" class="btn btn-secondary">Annuler</a>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- JS SCRIPTS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
