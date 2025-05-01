<?php
require_once '../../../controller/PostController.php';

$successMessage = '';
$errorMessage = '';

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $text = $_POST['text'];
    $id_u = 1;  // ID utilisateur (exemple)

    // Gestion de l'upload du fichier
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

    // Ajout du post via le contrôleur
    try {
        $controller = new PostController();
        if ($controller->addPost($titre, $text, $jointure)) {
            $successMessage = "Post ajouté avec succès!";
        }
    } catch (Exception $e) {
        // Si l'ID existe déjà ou autre erreur, afficher le message d'erreur
        $errorMessage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Post - Let's Link</title>
    
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #06BBCC;
            --secondary-color: #f8f9fa;
            --accent-color: #ff6f61;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 30px;
        }
        
        .form-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(6, 187, 204, 0.25);
        }
        
        .btn-submit {
            background-color: var(--primary-color);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-submit:hover {
            background-color: #0596a5;
            transform: translateY(-2px);
        }
        
        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: block;
            padding: 15px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-upload-label:hover {
            border-color: var(--primary-color);
            background: #f1f1f1;
        }
        
        .file-upload-icon {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .alert {
            border-radius: 10px;
        }
    </style>

<script>
function validateForm(event) {
    const titre = document.getElementById("titre").value.trim();
    const text = document.getElementById("text").value.trim();
    const jointureInput = document.getElementById("jointure");
    const jointure = jointureInput.value.trim();
    let errorMessage = "";

    // 1. Si tous les champs sont vides
    if (!titre || !text || !jointure) {
        alert("Tu dois remplir tous les champs.");
        event.preventDefault();
        return false;
    }

    // 2. Vérifie les champs remplis
    const titreRegex = /^[a-zA-ZÀ-ÿ\s]+$/;

    if (titre && (titre.length < 3 || !titreRegex.test(titre))) {
        errorMessage += "Le titre doit contenir au moins 3 lettres uniquement.\n";
        document.getElementById("titre").style.borderColor = "red";
    } else {
        document.getElementById("titre").style.borderColor = "";
    }

    if (text && text.length < 3) {
        errorMessage += "Le contenu doit contenir au moins 3 caractères.\n";
        document.getElementById("text").style.borderColor = "red";
    } else {
        document.getElementById("text").style.borderColor = "";
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
    }

    // 3. Affiche les erreurs si besoin
    if (errorMessage) {
        alert(errorMessage);
        event.preventDefault();
        return false;
    }

    // 4. Si tout est ok
    alert("Post ajouté avec succès !");
    setTimeout(() => {
        window.location.href = "commentaire.php";
    }, 1500);

    return true;
}
</script>



</head>

<body>
    <!-- Topbar Start -->
    <div class="container-fluid bg-dark text-light px-0 py-2">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <span class="fa fa-phone-alt me-2"></span>
                    <span>+216 97640509</span>
                </div>
                <div class="h-100 d-inline-flex align-items-center">
                    <span class="far fa-envelope me-2"></span>
                    <span>letslink@gmail.com</span>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center mx-n2">
                    <span>Follow Us:</span>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
        <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img src="logo.png" alt="logo" style="width:140px; height:auto;">
        </a>
        
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.html" class="nav-item nav-link">Home</a>
                <a href="about.html" class="nav-item nav-link">About</a>
                <a href="service.html" class="nav-item nav-link">Services</a>
                <a href="project.html" class="nav-item nav-link active">Blog</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu bg-light m-0">
                        <a href="commentaire.php" class="dropdown-item">Commentaires</a>
                    </div>
                </div>
                <a href="contact.html" class="nav-item nav-link">Contact</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white mb-4 animated slideInDown">Créer un nouveau post</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Blog</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nouveau post</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Main Form Container -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container wow fadeInUp" data-wow-delay="0.3s">
                    <!-- Messages d'alerte -->
                    <h2 class="form-title">Partagez avec la communauté</h2>

                    <form name="postForm" action="contact.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <!-- Titre -->
                        <div class="mb-4">
                            <label for="titre" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="titre" name="titre" placeholder="Donnez un titre accrocheur à votre post">
                        </div>

                        <!-- Texte -->
                        <div class="mb-4">
                            <label for="text" class="form-label">Contenu</label>
                            <textarea class="form-control" id="text" name="text" rows="6" placeholder="Exprimez-vous..."></textarea>
                        </div>

                        <!-- Upload Fichier -->
                        <div class="mb-4">
                            <label class="form-label">Image ou Vidéo (optionnel)</label>
                            <input type="file" class="form-control" id="jointure" name="jointure" accept="image/*,video/*">
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-paper-plane me-2"></i> Publier le post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
