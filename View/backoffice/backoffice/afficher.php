<?php
require_once 'C:\xampp\htdocs\mon_project_web\config.php';
require_once 'C:\xampp\htdocs\mon_project_web\model\utilisateur.php';
require_once 'C:\xampp\htdocs\mon_project_web\controller\UtilisateurC.php';

$utilisateurC = new UtilisateurC();
$tri = isset($_GET['tri']) ? $_GET['tri'] : null;
$utilisateurs = $utilisateurC->afficherUtilisateurs($tri);

// Export PDF pour un seul utilisateur
if (isset($_GET['export_user']) && is_numeric($_GET['export_user'])) {
    require_once 'C:\xampp\htdocs\mon_project_web\config.php';
    require_once 'C:\xampp\htdocs\mon_project_web\model\utilisateur.php';
    require_once 'C:\xampp\htdocs\mon_project_web\controller\UtilisateurC.php';
    require_once 'C:\xampp\htdocs\mon_project_web\TCPDF-main\tcpdf.php';

    $utilisateurC = new UtilisateurC();
    $userId = (int)$_GET['export_user'];
    $user = $utilisateurC->getUtilisateurById($userId);

    if ($user) {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // ✅ Text info to encode in QR code (no URL)
        $userDataForQR = "Fiche Utilisateur\n";
        $userDataForQR .= "ID: {$user['id']}\n";
        $userDataForQR .= "Nom: {$user['nom']}\n";
        $userDataForQR .= "Prénom: {$user['prenom']}\n";
        $userDataForQR .= "Email: {$user['email']}\n";
        $userDataForQR .= "Téléphone: {$user['telephone']}\n";
        $userDataForQR .= "Adresse: {$user['adresse']}\n";
        $userDataForQR .= "Rôle: {$user['role']}\n";
        $userDataForQR .= "Statut: " . (($user['actif'] ?? 1) ? 'Actif' : 'Inactif');

        // CSS style
        $style = '
        <style>
            .header { color: #2d5bd8; font-size: 18px; font-weight: bold; margin-bottom: 10px; }
            .table { border-collapse: collapse; width: 100%; margin-bottom: 15px; }
            .table th { background-color: #f2f2f2; text-align: left; padding: 8px; width: 30%; }
            .table td { padding: 8px; border-bottom: 1px solid #ddd; }
            .status-active { color: #1cc88a; font-weight: bold; }
            .status-inactive { color: #e74a3b; font-weight: bold; }
            .qr-container { text-align: center; margin: 15px 0; }
        </style>
        ';

        // HTML content for the PDF
        $html = $style . '
        <div class="header">Fiche Utilisateur</div>
        <table class="table">
            <tr><th>ID</th><td>' . htmlspecialchars($user['id']) . '</td></tr>
            <tr><th>Nom</th><td>' . htmlspecialchars($user['nom']) . '</td></tr>
            <tr><th>Prénom</th><td>' . htmlspecialchars($user['prenom']) . '</td></tr>
            <tr><th>Email</th><td>' . htmlspecialchars($user['email']) . '</td></tr>
            <tr><th>Téléphone</th><td>' . htmlspecialchars($user['telephone']) . '</td></tr>
            <tr><th>Adresse</th><td>' . htmlspecialchars($user['adresse']) . '</td></tr>
            <tr><th>Rôle</th><td>' . htmlspecialchars($user['role']) . '</td></tr>
            <tr><th>Statut</th><td><span class="' . (($user['actif'] ?? 1) ? 'status-active' : 'status-inactive') . '">' 
                . (($user['actif'] ?? 1) ? 'Actif' : 'Inactif') . '</span></td></tr>
        </table>

        <div class="qr-container">
            <b>QR Code contenant les mêmes informations</b>
        </div>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // QR Code with user info text
        $qrCodeParams = [
            'border' => 1,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1
        ];
        $pdf->write2DBarcode($userDataForQR, 'QRCODE,L', 75, $pdf->GetY(), 60, 60, $qrCodeParams, 'N');
        $pdf->SetY($pdf->GetY() + 65);

        // Footer
        $pdf->writeHTML('<div style="text-align:center; font-size:10px; color:#888;">'
            . 'Généré le ' . date('d/m/Y à H:i') . ' - Pure Vibe</div>', true, false, true, false, '');

        $pdf->Output('utilisateur_' . $user['id'] . '.pdf', 'D');
        exit;
    }
}

// Export PDF global (votre code existant)
if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    require_once 'C:\xampp\htdocs\mon_project_web\TCPDF-main\tcpdf.php';
    
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $html = '<h1>Liste des Utilisateurs</h1>
    <table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Adresse</th>
        <th>Rôle</th>
        <th>Statut</th>
    </tr>';

    foreach ($utilisateurs as $user) {
        $html .= '<tr>
            <td>' . htmlspecialchars($user['id']) . '</td>
            <td>' . htmlspecialchars($user['nom']) . '</td>
            <td>' . htmlspecialchars($user['prenom']) . '</td>
            <td>' . htmlspecialchars($user['email']) . '</td>
            <td>' . htmlspecialchars($user['telephone']) . '</td>
            <td>' . htmlspecialchars($user['adresse']) . '</td>
            <td>' . htmlspecialchars($user['role']) . '</td>
            <td>' . (($user['actif'] ?? 1) ? 'Actif' : 'Inactif') . '</td>
        </tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('liste_utilisateurs.pdf', 'D');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pure Vibe - Liste des Utilisateurs</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .bg-gradient-primary { background-color: rgb(45, 91, 216) !important; background-image: none !important; }
        .dynamic-frame { border-left: 4px solid #e74a3b; transition: all 0.3s; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .badge-active { background-color: #1cc88a; }
        .badge-inactive { background-color: #e74a3b; }
        .export-buttons .btn { margin-right: 10px; }
        .phone-number { direction: ltr; text-align: right; }
    </style>
</head>
<body id="page-top">
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <img src="img/logo.png" alt="logo" width="150" height="60">
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link" href="index.html"><i class="fas fa-fw fa-tachometer-alt"></i><span>Tableau De Bord</span></a>
        </li>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Interface</div>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#usersMenu" aria-expanded="true" aria-controls="usersMenu">
                <i class="fas fa-fw fa-users"></i><span>Gestion des Utilisateurs</span>
            </a>
            <div id="usersMenu" class="collapse show" aria-labelledby="usersMenu">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="ajouter.php"><i class="fas fa-plus-circle mr-2"></i>Ajouter</a>
                    <a class="collapse-item" href="modifier.php"><i class="fas fa-edit mr-2"></i>Modifier</a>
                    <a class="collapse-item" href="supprimer.php"><i class="fas fa-trash-alt mr-2"></i>Supprimer</a>
                    <a class="collapse-item active" href="afficher.php"><i class="fas fa-eye mr-2"></i>Affichage</a>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>
    <!-- End of Sidebar -->

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Administrateur</span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Profil</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Déconnexion</a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Liste des Utilisateurs</h1>
                    <div class="export-buttons">
                        <button class="btn btn-success" onclick="exportToExcel()"><i class="fas fa-file-excel mr-1"></i> Excel</button>
                        <a href="afficher.php?export=pdf" class="btn btn-danger"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card shadow mb-4 dynamic-frame">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Tous les utilisateurs</h6>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" id="searchButton"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom complet</th>
                                                <th>Email</th>
                                                <th>Téléphone</th>
                                                <th>Adresse</th>
                                                <th>Rôle</th>
                                                <th>Statut</th>
                                            </tr>
                                            
                                        </thead>
                                        <tbody>
                                            <?php foreach ($utilisateurs as $u): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($u['id']) ?></td>
                                                <td><?= htmlspecialchars($u['nom']) . ' ' . htmlspecialchars($u['prenom']) ?></td>
                                                <td><?= htmlspecialchars($u['email']) ?></td>
                                                <td class="phone-number"><?= htmlspecialchars($u['telephone']) ?></td>
                                                <td><?= htmlspecialchars($u['adresse']) ?></td>
                                                <td><?= htmlspecialchars($u['role']) ?></td>
                                                <td><span class="badge <?= ($u['actif'] ?? 1) ? 'badge-active' : 'badge-inactive' ?>"><?= ($u['actif'] ?? 1) ? 'Actif' : 'Inactif' ?></span></td>
                                                <td>
    
                                                <a href="afficher.php?export_user=<?= $u['id'] ?>" class="btn btn-sm btn-info">
    <i class="fas fa-file-pdf"></i> PDF
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
            </div>
        </div>
<form method="GET" action="afficher.php">
    <button type="submit" name="tri" value="ASC">Trier par nom croissante</button>
    <button type="submit" name="tri" value="DESC">Trier par nom décroissante</button>
</form>
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Pure Vibe 2023</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Prêt à partir?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body">Sélectionnez "Déconnexion" ci-dessous si vous êtes prêt à terminer votre session actuelle.</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
        <a class="btn btn-primary" href="login.html">Déconnexion</a>
      </div>
    </div>
  </div>
</div>


<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    const table = $('#dataTable').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        responsive: true
    });

    $('#searchButton').on('click', function() {
        table.search($('#searchInput').val()).draw();
    });

    $('#searchInput').on('keyup', function(e) {
        if (e.key === 'Enter') {
            table.search($(this).val()).draw();
        }
    });
});

function exportToExcel() {
    // Créer un fichier Excel avec les données du tableau
    let csv = 'ID,Nom complet,Email,Téléphone,Adresse,Rôle,Statut\n';
    
    $('#dataTable tbody tr').each(function() {
        const cells = $(this).find('td');
        csv += `"${cells.eq(0).text()}","${cells.eq(1).text()}","${cells.eq(2).text()}","${cells.eq(3).text()}","${cells.eq(4).text()}","${cells.eq(5).text()}","${cells.eq(6).text()}"\n`;
    });
    
    // Créer un lien de téléchargement
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'liste_utilisateurs.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
</body>
</html>



