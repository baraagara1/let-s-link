<?php
require_once '../../../controller/EventController.php';
require_once '../../../controller/ActiviteController.php';
$controller = new EventController();
$id_a = $_GET['id_a'] ?? null;

$activiteController = new ActiviteController();
$listeActivites = $activiteController::listActivites(); // récupère toutes les activités dans un tableau associatif
$activiteMap = [];
foreach ($listeActivites as $act) {
    $activiteMap[$act['id_a']] = $act['nom_a'];
}
$orderBy = $_GET['sort_by'] ?? 'date_e';
$direction = $_GET['dir'] ?? 'ASC';

if ($id_a !== null) {
    $events = $controller->getEventsByActivite($id_a);
} else {
    $events = $controller->listEventsSorted($orderBy, $direction);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Événements | Admin</title>
    <link rel="icon" type="image/png" href="img/logo.png">

    <!-- Fonts et styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #ff6b6b;
            --dark-color: #5a5c69;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
        }
        
        .bg-gradient-primary {
            background-color: var(--primary-color) !important;
            background-image: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%) !important;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #3a5ccc;
            border-color: #3a5ccc;
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-success:hover {
            background-color: #17a673;
            border-color: #17a673;
        }
        
        .btn-warning {
            background-color: #f6c23e;
            border-color: #f6c23e;
            color: #fff;
        }
        
        .btn-warning:hover {
            background-color: #dda20a;
            border-color: #dda20a;
            color: #fff;
        }
        
        .btn-danger {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-danger:hover {
            background-color: #e05555;
            border-color: #e05555;
        }
        
        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }
        
        .btn-info:hover {
            background-color: #2c9faf;
            border-color: #2c9faf;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 15px;
        }
        
        .table td {
            vertical-align: middle;
            padding: 12px 15px;
            border-top: 1px solid #e3e6f0;
        }
        
        .table tr:hover td {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .event-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .event-image:hover {
            transform: scale(1.1);
            cursor: pointer;
        }
        
        .action-btn {
            margin: 2px;
            min-width: 80px;
            border-radius: 20px;
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        
        .search-container {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .search-container i {
            position: absolute;
            left: 12px;
            top: 12px;
            color: #d1d3e2;
        }
        
        .search-input {
            padding-left: 35px;
            border-radius: 20px;
            border: 1px solid #d1d3e2;
            height: 40px;
            width: 300px;
        }
        
        .add-btn {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(78, 115, 223, 0.3);
            transition: all 0.3s ease;
        }
        
        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
        }
        
        .badge-pill {
            padding: 5px 10px;
            font-size: 0.75rem;
        }
        
        .status-badge {
            background-color: var(--success-color);
        }
        
        .no-image {
            color: #b7b9cc;
            font-style: italic;
        }
        
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .action-cell {
            min-width: 220px;
        }
        
        .date-badge {
            background-color: #f8f9fc;
            color: var(--dark-color);
            border: 1px solid #e3e6f0;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .ai-generate-btn {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }
        
        .ai-generate-btn:hover {
            background-color: #5a32a3;
            border-color: #5a32a3;
        }
        
        .type-badge {
            background-color: #e83e8c;
            color: white;
        }
        
        .place-icon {
            color: var(--accent-color);
            margin-right: 5px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

</head>

<body id="page-top" class="sidebar-toggled">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <img src="img/logo.png" alt="logo" width="40">
                </div>
                <div class="sidebar-brand-text mx-2">Admin Panel</div>
            </a>
            <hr class="sidebar-divider my-0">
            <div class="sidebar-heading">Interface</div>
            <li class="nav-item">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tableau De Bord</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item active">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Gestion des activitées</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">

                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion:</h6>
                        <a class="collapse-item active" href="Evénements.php">Activités</a>
                        <a class="collapse-item" href="activites.php">Événements</a>
                    </div>
                </div>
            </li>
            <hr class="sidebar-divider">
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    
                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Rechercher..." id="globalSearchInput">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Administrateur</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Paramètres
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Déconnexion
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Begin Page Content -->
                <div class="container-fluid animate__animated animate__fadeIn">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Gestion des Activitées</h1>
                        <div>
                            <?php if (isset($id_a)): ?>
                                <a href="ajouter_evenement.php?id_a=<?= $id_a ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm add-btn">
                                    <i class="fas fa-plus fa-sm text-white-50"></i> Ajouter un activitée
                                </a>
                            <?php else: ?>
                                <a href="ajouter_evenement.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm add-btn">
                                    <i class="fas fa-plus fa-sm text-white-50"></i> Ajouter un activitées
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Liste des activitées</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Options:</div>
                                    <button id="downloadPDF">Exporter en PDF</button>


                                    <a class="dropdown-item" href="#">Exporter en Excel</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Actualiser</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                        <th>
  <a href="?sort_by=nom_e&dir=<?= $orderBy === 'nom_e' && $direction === 'ASC' ? 'DESC' : 'ASC' ?>" style="color: white;">
    Nom <?= $orderBy === 'nom_e' ? ($direction === 'ASC' ? '▲' : '▼') : '' ?>
  </a>
</th>
<th>Type</th>
<th>
  <a href="?sort_by=date_e&dir=<?= $orderBy === 'date_e' && $direction === 'ASC' ? 'DESC' : 'ASC' ?>" style="color: white;">
    Date <?= $orderBy === 'date_e' ? ($direction === 'ASC' ? '▲' : '▼') : '' ?>
  </a>
</th>


                                            <th>Lieu</th>
                                            <th>Image</th>
                                            <th class="action-cell">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($events as $event): ?>
                                            <tr data-id="<?php echo $event['id_e']; ?>" data-name="<?php echo htmlspecialchars(strtolower($event['nom_e'])); ?>">
                                                <td>
                                                    <strong><?= htmlspecialchars($event['nom_e']) ?></strong>
                                                    
                                                </td>
                                                <td>
                                                    <span class="badge badge-pill type-badge">
                                                    <?= htmlspecialchars($activiteMap[$event['id_a']] ?? 'Activité inconnue') ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="date-badge">
                                                        <i class="far fa-calendar-alt mr-1"></i>
                                                        <?= date('d/m/Y', strtotime($event['date_e'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-map-marker-alt place-icon"></i>
                                                    <?= htmlspecialchars($event['lieu_e']) ?>
                                                </td>
                                                <td>
                                                    <?php if ($event['image']): ?>
                                                        <img src="../../uploads/<?= $event['image'] ?>" 
                                                             class="event-image" 
                                                             alt="Image de l'événement"
                                                             data-toggle="tooltip" 
                                                             data-placement="top" 
                                                             title="Voir en grand"
                                                             onclick="showImageModal('../../uploads/<?= $event['image'] ?>')">
                                                    <?php else: ?>
                                                        <span class="no-image"><i class="fas fa-image"></i> Pas d'image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap">
                                                        <a href="modifier_evenement.php?id=<?= $event['id_e'] ?>" 
                                                           class="btn btn-warning btn-sm action-btn"
                                                           data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="supprimer_evenement.php?id=<?= $event['id_e'] ?>" 
                                                           class="btn btn-danger btn-sm action-btn"
                                                           data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Supprimer"
                                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet activitées?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                        <a href="details_evenement.php?id=<?= $event['id_e'] ?>" 
                                                           class="btn btn-info btn-sm action-btn"
                                                           data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Détails">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <!-- Bouton IA uniquement pour les événements futurs -->
                                                        <?php if (strtotime($event['date_e']) >= strtotime(date('Y-m-d'))): ?>
    <button 
        class="btn btn-secondary btn-sm action-btn open-ia-modal" 
        data-id="<?= $event['id_e'] ?>"
        data-toggle="tooltip" 
        title="Suggestions IA">
        <i class="fas fa-robot"></i>
    </button>
<?php endif; ?>

                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <?php if (isset($id_a)): ?>
                                <a href="ajouter_evenement.php?id_a=<?= $id_a ?>" class="btn btn-success btn-icon-split add-btn">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                    <span class="text">Ajouter une activitées</span>
                                </a>
                            <?php else: ?>
                                <a href="ajouter_evenement.php" class="btn btn-success btn-icon-split add-btn">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                    <span class="text">Ajouter une activitées</span>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($listeActivites)): ?>

                            <button id="generate-ai-btn" class="btn btn-primary ai-generate-btn btn-icon-split">
    <span class="icon text-white-50">
        <i class="fas fa-robot"></i>
    </span>
    <span class="text">Générer avec IA</span>
</button>

                        <?php endif; ?>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Votre Site 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Prêt à partir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Sélectionnez "Déconnexion" ci-dessous si vous êtes prêt à terminer votre session en cours.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                    <a class="btn btn-primary" href="login.html">Déconnexion</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image de l'activitées</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Image agrandie">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <h5 class="text-primary">Génération des activitées en cours...</h5>
                    <p>Veuillez patienter pendant que l'IA crée des activitées pertinents.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Scripts personnalisés -->
    <script>
        // Initialisation des tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        
        
        
        // Fonction pour afficher l'image en grand
        function showImageModal(imageSrc) {
            $('#modalImage').attr('src', imageSrc);
            $('#imageModal').modal('show');
        }
        
        // Gestion de la génération IA
        document.getElementById('generate-ai-btn')?.addEventListener('click', async () => {
    const id_a = <?= isset($id_a) ? $id_a : 'null' ?>;

    if (!id_a) {
        alert("Aucune activité sélectionnée. Sélectionnez une activité avant de générer.");
        return;
    }

    const confirmed = confirm("Tu es sûr de vouloir générer automatiquement 2 activitées via l'IA ?");
    if (!confirmed) return;

    try {
        const response = await fetch('generate_event.php?id_a=' + id_a);
        const rawText = await response.text();
        console.log("Réponse IA:", rawText);

        const result = JSON.parse(rawText);

        if (result.success) {
            alert("✅ Les activitées ont été générés avec succès !");
            location.reload();
        } else {
            alert("❌ Erreur : " + (result.error || 'Réponse invalide'));
        }
    } catch (error) {
        console.error("Erreur IA:", error);
        alert("Une erreur s'est produite lors de la génération des activitées.");
    }
});

    </script>
    <script>
    document.getElementById("downloadPDF").addEventListener("click", () => {
        const element = document.querySelector(".table-responsive"); // ou ton tableau complet
        html2pdf().from(element).save("evenements.pdf");
    });
</script>
<script>
document.querySelectorAll(".open-ia-modal")?.forEach(btn => {
    btn.addEventListener("click", async () => {
        const id = btn.getAttribute("data-id");
        const modal = $('#iaSuggestionModal');
        document.getElementById("ia-suggestion-content").innerHTML = `
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3">Analyse en cours, veuillez patienter...</p>
        `;
        modal.modal('show');

        try {
            const res = await fetch('ai_suggestion.php?id_e=' + id);
            const rawResponse = await res.text(); // Récupérer la réponse brute
            console.log("Réponse brute:", rawResponse); // Afficher la réponse brute dans la console

            let jsonResponse;
            try {
                jsonResponse = JSON.parse(rawResponse); // Tenter de la parser
            } catch (e) {
                console.error("Erreur de parsing JSON:", e);
                jsonResponse = []; // Si le parsing échoue, retournez un tableau vide ou un autre comportement
            }

            if (jsonResponse.length === 0) {
                document.getElementById("ia-suggestion-content").innerHTML = `
                    <p class="text-muted">Aucune suggestion trouvée pour cet événement.</p>
                `;
            } else {
                document.getElementById("ia-suggestion-content").innerHTML = `
                    <ul class="list-group text-left">
                       ${jsonResponse.map(user => `<li class="list-group-item" data-id="${user.id_u}">Nom: <strong>${user.nom_u}</strong></li>`).join('')}

                    </ul>
                `;
            }
        } catch (e) {
            document.getElementById("ia-suggestion-content").innerHTML = `
                <div class="alert alert-danger">Erreur lors de la récupération des suggestions IA.</div>
            `;
            console.error("Erreur:", e);
        }
    });
});




</script>
<script>
async function sendInvitations(ids) {
    try {
        const res = await fetch('api/send_invites.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(ids)
        });

        const raw = await res.text();
        console.log("Réponse brute :", raw);

        const result = JSON.parse(raw);

        if (result.envoyes?.length) {
            alert("✅ Invitations envoyées à : " + result.envoyes.join(", "));
        }

        if (result.echoues?.length) {
            alert("❌ Erreurs pour : " + result.echoues.join(", "));
        }

    } catch (e) {
        console.error("Erreur lors de l'envoi :", e);
        alert("Erreur lors de l'envoi des mails.");
    }
}
</script>

<script>
function inviteSuggestedUsers() {
    const items = document.querySelectorAll("#ia-suggestion-content li");
    const ids = Array.from(document.querySelectorAll("#ia-suggestion-content li"))
    .map(li => parseInt(li.dataset.id))
    .filter(Boolean);


    if (ids.length > 0) {
        sendInvitations(ids); // <- appelle la fonction qu'on a déjà ajoutée
    } else {
        alert("Aucun utilisateur à inviter.");
    }
}
</script>


<!-- IA Suggestion Modal -->
<div class="modal fade" id="iaSuggestionModal" tabindex="-1" role="dialog" aria-labelledby="iaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="iaModalLabel">Suggestions IA</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="ia-suggestion-content" class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Chargement...</span>
          </div>
          <p class="mt-3">Analyse en cours, veuillez patienter...</p>
        </div>
        <button class="btn btn-primary mt-3" onclick="inviteSuggestedUsers()">Inviter les utilisateurs suggérés</button>

      </div>
    </div>
  </div>
</div>


</body>
</html>