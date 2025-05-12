<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un Covoiturage</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #4e73df;
    }
    .bg-gradient-primary {
      background-color: var(--primary) !important;
      background-image: none !important;
    }
    .alert-box {
      background: #f8d7da;
      color: #721c24;
      padding: 15px;
      border: 1px solid #f5c6cb;
      border-radius: 5px;
      margin-bottom: 20px;
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

    <!-- Contenu -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
          <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3">
            <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" placeholder="Recherche...">
              <div class="input-group-append"><button class="btn btn-primary" type="button"><i class="fas fa-search fa-sm"></i></button></div>
            </div>
          </form>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid">
          <h1 class="h3 mb-4 text-gray-800">Ajouter un Covoiturage</h1>
          <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Formulaire d'ajout</h6></div>
            <div class="card-body">

              <?php
              if (!empty($_SESSION['form_errors'])) {
                echo '<div class="alert-box"><strong>Erreurs :</strong><ul>';
                foreach ($_SESSION['form_errors'] as $error) {
                  echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul></div>';
                unset($_SESSION['form_errors']);
              }

              $old = $_SESSION['old_values'] ?? [];
              unset($_SESSION['old_values']);
              ?>

              <form class="user" action="index.php?action=traiter_ajout" method="POST">
                <div class="form-group">
                  <label for="lieu_depart">Lieu de départ</label>
                  <input class="form-control" name="lieu_depart" value="<?= htmlspecialchars($old['lieu_depart'] ?? '') ?>">
                </div>

                <div class="form-group">
                  <label for="destination">Destination</label>
                  <input class="form-control" name="destination" value="<?= htmlspecialchars($old['destination'] ?? '') ?>">
                </div>

                <div class="form-group">
                  <label for="date">Date</label>
                  <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($old['date'] ?? '') ?>">
                </div>

                <div class="form-group">
                  <label for="heure">Heure de départ</label>
                  <input type="time" class="form-control" name="heure" value="<?= htmlspecialchars($old['heure'] ?? '') ?>">
                </div>

                <div class="form-group">
                  <label for="place_dispo">Places disponibles</label>
                  <input class="form-control" name="place_dispo" value="<?= htmlspecialchars($old['place_dispo'] ?? '') ?>">
                </div>

                <div class="form-group">
                  <label for="prix_c">Prix</label>
                  <input class="form-control" name="prix_c" value="<?= htmlspecialchars($old['prix_c'] ?? '') ?>">
                </div>

                <div class="form-group">
                  <label for="user_id">ID Utilisateur</label>
                  <input class="form-control" name="user_id" value="<?= htmlspecialchars($old['user_id'] ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-user">Ajouter</button>
              </form>

            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto text-center"><span>&copy; LET’S LINK 2025</span></div>
      </footer>

    </div>
  </div>

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
