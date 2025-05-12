<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Demandes de Suppression</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .bg-gradient-primary { background-color: #4e73df !important; background-image: none; }
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
          <h1 class="h3 mb-4 text-gray-800">Demandes de suppression</h1>
          <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Liste des demandes</h6></div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                <thead class="thead-light bg-primary text-white">
                <tr>
                      <th>ID</th>
                      <th>ID Réservation</th>
                      <th>Statut</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($demandes as $demande): ?>
                      <tr>
                        <td><?= $demande['id'] ?></td>
                        <td><?= $demande['reservation_id'] ?></td>
                        <td>
                          <?php if ($demande['statut'] === 'En attente'): ?>
                            <span class="badge badge-warning">En attente</span>
                          <?php elseif ($demande['statut'] === 'Acceptée'): ?>
                            <span class="badge badge-success">Acceptée</span>
                          <?php else: ?>
                            <span class="badge badge-danger">Refusée</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($demande['statut'] === 'En attente'): ?>
                            <a href="index.php?action=accepter&id=<?= $demande['id'] ?>" class="btn btn-success btn-sm">Accepter</a>
                            <a href="index.php?action=refuser&id=<?= $demande['id'] ?>" class="btn btn-danger btn-sm">Refuser</a>
                          <?php else: ?>
                            <em>Aucune action</em>
                          <?php endif; ?>
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

  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
