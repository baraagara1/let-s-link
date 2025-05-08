<?php
require_once '../../../controller/PostController.php';

// Récupérer tous les posts
$controller = new PostController();
$posts = $controller->getAllPosts();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Posts | Admin</title>
    <link rel="icon" type="image/png" href="img/logo.png">

    <!-- Fonts et styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

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
        
        .media-preview {
            width: 120px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .media-preview:hover {
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
        
        .no-media {
            color: #b7b9cc;
            font-style: italic;
        }
        
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .action-cell {
            min-width: 200px;
        }
        
        .text-preview {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 4.5em;
            line-height: 1.5em;
        }
        
        .title-with-id {
            display: flex;
            flex-direction: column;
        }
        
        .post-id {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
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
            <li class="nav-item active">
                <a class="nav-link" href="gestion_blog.php">
                    <i class="fas fa-fw fa-blog"></i>
                    <span>Gestion des Blogs</span>
                </a>
                <a class="nav-link" href="gestion_commentaire.php">
                    <i class="fas fa-fw fa-blog"></i>
                    <span>Gestion des Commentaires</span>
                </a>
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
                        <h1 class="h3 mb-0 text-gray-800">Gestion des Posts</h1>
                        <a href="ajouter_post.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm add-btn">
                            <i class="fas fa-plus fa-sm text-white-50"></i> Ajouter un Post
                        </a>
                    </div>

                    <!-- Success or Error Message -->
                    <?php if (isset($successMessage)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $successMessage; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php elseif (isset($errorMessage)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $errorMessage; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Liste des Posts</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Options:</div>
                                    <a href="export_pdf.php" class="dropdown-item">Exporter en PDF</a>


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
                                            <th>Titre</th>
                                            <th>Contenu</th>
                                            <th>Média</th>
                                            <th class="action-cell">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($posts as $post): ?>
                                            <tr data-id="<?php echo $post['id_p']; ?>">
                                                <td>
                                                    <div class="title-with-id">
                                                        <strong><?php echo htmlspecialchars($post['titre']); ?></strong>
                                                       
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-preview">
                                                        <?php echo htmlspecialchars($post['text']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($post['jointure'] !== null) {
                                                        $filePath = '../../uploads/' . $post['jointure'];
                                                        $fileExt = pathinfo($filePath, PATHINFO_EXTENSION);

                                                        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                                            echo '<img src="' . $filePath . '" class="media-preview" alt="Image du post" onclick="showImageModal(\'' . $filePath . '\')">';
                                                        } elseif (in_array($fileExt, ['mp4', 'avi', 'mov', 'mkv'])) {
                                                            echo '<video class="media-preview" onclick="showMediaModal(\'' . $filePath . '\', \'video\')">
                                                                    <source src="' . $filePath . '" type="video/' . $fileExt . '">
                                                                  </video>';
                                                        }
                                                    } else {
                                                        echo '<span class="no-media"><i class="fas fa-image"></i> Aucun média</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap">
                                                        <a href="modifier_post.php?id=<?php echo $post['id_p']; ?>" 
                                                           class="btn btn-warning btn-sm action-btn"
                                                           data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="supprimer_post.php?id=<?php echo $post['id_p']; ?>" 
                                                           class="btn btn-danger btn-sm action-btn"
                                                           data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Supprimer"
                                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce post ?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                        <a href="details_post.php?id=<?php echo $post['id_p']; ?>" 
                                                           class="btn btn-info btn-sm action-btn"
                                                           data-toggle="tooltip"
                                                           data-placement="top"
                                                           title="Détails">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <a href="ajouter_post.php" class="btn btn-success btn-icon-split add-btn mb-4">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Ajouter un Post</span>
                    </a>
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

    <!-- Media Modal -->
    <div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="mediaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediaModalLabel">Média du Post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="mediaModalContent">
                    <!-- Content will be inserted here by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
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
        
        // Initialisation de DataTables
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
                },
                "responsive": true,
                "order": [[0, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": [2, 3] } // Désactiver le tri sur les colonnes Média et Actions
                ]
            });
            
            // Recherche globale
            $('#globalSearchInput').keyup(function(){
                table.search($(this).val()).draw();
            });
        });
        
        // Fonction pour afficher l'image en grand
        function showImageModal(imageSrc) {
            $('#mediaModalContent').html('<img src="' + imageSrc + '" class="img-fluid" alt="Image agrandie">');
            $('#mediaModal').modal('show');
        }
        
        // Fonction pour afficher la vidéo en grand
        function showMediaModal(mediaSrc, type) {
            if (type === 'video') {
                $('#mediaModalContent').html('<video width="100%" controls autoplay>' +
                                           '<source src="' + mediaSrc + '" type="video/' + mediaSrc.split('.').pop() + '">' +
                                           'Votre navigateur ne supporte pas les vidéos.' +
                                           '</video>');
            } else {
                $('#mediaModalContent').html('<img src="' + mediaSrc + '" class="img-fluid" alt="Média agrandi">');
            }
            $('#mediaModal').modal('show');
        }
    </script>
<script>
    document.getElementById("downloadPDF").addEventListener("click", () => {
        const element = document.querySelector(".table-responsive"); // ou ton tableau complet
        html2pdf().from(element).save("evenements.pdf");
    });
</script>

</body>
</html>