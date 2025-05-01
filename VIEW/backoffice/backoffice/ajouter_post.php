<?php 
require_once '../../../controller/PostController.php';

$successMessage = '';
$errorMessage = '';

// Traitement sans validation PHP (tout est validé en JS)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $text = $_POST['text'];

    if (isset($_FILES['jointure'])) {
        $uploadDir = '../../uploads/';
        $fileName = basename($_FILES['jointure']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['jointure']['tmp_name'], $uploadFile)) {
            $jointure = $fileName;
        } else {
            $errorMessage = "Erreur lors du téléchargement du fichier.";
            $jointure = null;
        }
    } else {
        $jointure = null;
    }

    try {
        $controller = new PostController();
        if ($controller->addPost($titre, $text, $jointure)) {
            $successMessage = "Post ajouté avec succès!";
        } else {
            $errorMessage = "Une erreur est survenue lors de l'ajout du post.";
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Post</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script>
function validateForm(event) {
    const titre = document.getElementById("titre").value.trim();
    const text = document.getElementById("text").value.trim();
    const jointureInput = document.getElementById("jointure");
    const jointure = jointureInput.value;
    let errorMessage = "";

    // 1. Tous les champs sont vides ?
    if (!titre && !text ) {
        alert("Tous les champs sont vides. Veuillez remplir au moins un champ.");
        event.preventDefault();
        return false;}
    
    else{
    // 2. On vérifie les champs SEULEMENT s’ils sont remplis
    const titreRegex = /^[a-zA-ZÀ-ÿ\s]+$/;
    if (titre) {
        if (titre.length <= 2 || !titreRegex.test(titre)) {
            errorMessage += "Le titre doit contenir plus de 2 lettres uniquement.\n";
            document.getElementById("titre").style.borderColor = "red";
        } else {
            document.getElementById("titre").style.borderColor = "";
        }
    }

    if (text) {
        if (text.length <= 2) {
            errorMessage += "Le texte doit contenir plus de 2 caractères.\n";
            document.getElementById("text").style.borderColor = "red";
        } else {
            document.getElementById("text").style.borderColor = "";
        }
    }

    if (jointure) {
        const extension = jointure.split('.').pop().toLowerCase();
        const validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'mp4', 'avi', 'mov', 'mkv'];
        if (!validExtensions.includes(extension)) {
            errorMessage += "Le fichier doit être une image ou une vidéo valide.\n";
            jointureInput.style.borderColor = "red";
        } else {
            jointureInput.style.borderColor = "";
        }
    }}

    // 3. Afficher erreurs (si existantes)
    if (errorMessage) {
        alert(errorMessage);
        event.preventDefault();
    }
}

</script>

</head>

<body>
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <img src="img/logo.png" alt="logo" width="150" height="60">
        </a>
        <hr class="sidebar-divider my-0">
        <div class="sidebar-heading">Interface</div>
        <li class="nav-item">
            <a class="nav-link" href="gestion_blog.php">
                <i class="fas fa-fw fa-cog"></i>
                <span>Gestion des Blogs</span>
            </a>
        </li>
        <hr class="sidebar-divider">
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">utilisateur</span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid">
                <h1 class="h3 mb-2 text-gray-800">Ajouter un Post</h1>

                <!-- Message de succès ou erreur -->
                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?= $successMessage ?></div>
                <?php elseif ($errorMessage): ?>
                    <div class="alert alert-danger"><?= $errorMessage ?></div>
                <?php endif; ?>

                <!-- Formulaire -->
                <form name="postForm" action="ajouter_post.php" method="POST" enctype="multipart/form-data" onsubmit="validateForm(event)">
                    <div class="form-group row">
                        <div class="col-sm-12 mb-3 mb-sm-0">
                            <input type="text" class="form-control form-control-user" name="titre" id="titre" placeholder="Titre du Post">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-12 mb-3 mb-sm-0">
                            <textarea class="form-control form-control-user" name="text" id="text" rows="4" placeholder="Texte du Post"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="jointure">Image/Vidéo (facultatif) :</label>
                        <input type="file" class="form-control form-control-user" name="jointure" id="jointure" accept="image/*,video/*">
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-success btn-user btn-block">Ajouter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
