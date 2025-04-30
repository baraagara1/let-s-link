<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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


    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TOPBAR COMPLÈTE -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Barre de recherche -->
          <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" placeholder="Recherche..." aria-label="Search" aria-describedby="basic-addon2">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                  <i class="fas fa-search fa-sm"></i>
                </button>
              </div>
            </div>
          </form>

          <!-- Notifications -->
          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter">0</span>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Centre d'alertes</h6>
                <a class="dropdown-item text-center small text-gray-500" href="#">Aucune alerte</a>
              </div>
            </li>

            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown">
                <i class="fas fa-envelope fa-fw"></i>
                <span class="badge badge-danger badge-counter">0</span>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">Centre de messages</h6>
                <a class="dropdown-item text-center small text-gray-500" href="#">Aucun message</a>
              </div>
            </li>
          </ul>
        </nav>

        <!-- MAIN CONTENT -->
        <div class="container-fluid">
          <h1 class="h3 mb-4 text-gray-800">Ajouter un Covoiturage</h1>
          <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Formulaire d'ajout</h6></div>
            <div class="card-body">

              <!-- MESSAGE D'ERREUR PHP -->
              <?php
              if (!empty($_SESSION['form_errors'])) {
                echo '<div class="alert-box"><strong>Erreurs de saisie :</strong><ul>';
                foreach ($_SESSION['form_errors'] as $error) {
                  echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul></div>';
                unset($_SESSION['form_errors']);
              }

              $old = $_SESSION['old_values'] ?? [];
              unset($_SESSION['old_values']);
              ?>

              <form class="user" action="traitement-ajout.php" method="POST">
                <div class="form-group">
                  <label for="destination">Destination</label>
                  <input class="form-control form-control-user" id="destination" name="destination" placeholder="Ex: Marseille" value="<?= htmlspecialchars($old['destination'] ?? '') ?>">
                </div>

                <div class="form-group">
                  <label for="date">Date</label>
                  <input type="date" class="form-control form-control-user" id="date" name="date" value="<?= htmlspecialchars($old['date'] ?? '') ?>">
                </div>

                <div class="form-group">
                  <label for="place_dispo">Places disponibles</label>
                  <input class="form-control form-control-user" id="place_dispo" name="place_dispo" placeholder="Nombre de places" value="<?= htmlspecialchars($old['place_dispo'] ?? '') ?>">
                </div>

                <div class="form-group">
                  <label for="id_utilisateur">ID de l'utilisateur</label>
                  <input class="form-control form-control-user" id="id_utilisateur" name="id_utilisateur" placeholder="ID du conducteur" value="<?= htmlspecialchars($old['id_utilisateur'] ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-user">Ajouter</button>
              </form>
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

  <!-- SCRIPTS -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
