<?php
require_once '../../../controller/PostController.php';

$successMessage = '';
$errorMessage = '';

// Si l'ID du post est présent dans l'URL, récupérer l'information
if (isset($_GET['id'])) {
    $controller = new PostController();
    $post = $controller->getPostById($_GET['id']);  // Récupérer le post par son ID
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_p = $_POST['id_p'];  // L'ID du post reste caché mais est envoyé
    $titre = $_POST['titre'];  // Récupération du titre
    $text = $_POST['text'];

    // Gestion de l'upload du fichier (image/vidéo)
    if (isset($_FILES['jointure']) && $_FILES['jointure']['error'] == 0) {
        $uploadDir = '../../uploads/'; // Dossier de destination
        $fileName = basename($_FILES['jointure']['name']);
        $uploadFile = $uploadDir . $fileName;

        // Déplacer le fichier dans le dossier 'uploads'
        if (move_uploaded_file($_FILES['jointure']['tmp_name'], $uploadFile)) {
            $jointure = $fileName;  // Enregistrer seulement le nom du fichier
        } else {
            $errorMessage = "Erreur lors du téléchargement du fichier.";
            $jointure = $post['jointure'];  // Conserver l'ancienne image en cas d'erreur
        }
    } else {
        // Si aucun fichier n'est sélectionné, conserver l'ancien fichier (si présent)
        $jointure = $post['jointure'];
    }

    // Mettre à jour le post
    $controller = new PostController();
    if ($controller->updatePost($id_p, $titre, $text, $jointure)) {
        $successMessage = "Post mis à jour avec succès!";
        // Redirection vers le blog après 2 secondes
        header("Refresh: 2; URL=commentaire.php");
    } else {
        $errorMessage = "Erreur lors de la mise à jour du post.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Post - Let's Link</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
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
    <div class="container py-5">
        <h2>Modifier le Post</h2>
        
        <!-- Affichage des messages de succès ou d'erreur -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= $successMessage; ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="alert alert-danger"><?= $errorMessage; ?></div>
        <?php endif; ?>

        <form action="modifier_post.php?id=<?= $post['id_p']; ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
            <input type="hidden" name="id_p" value="<?= $post['id_p']; ?>">

            <!-- Titre -->
            <div class="mb-4">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" placeholder="Donnez un titre accrocheur à votre post" value="<?= htmlspecialchars($post['titre']); ?>">
            </div>

            <!-- Texte -->
            <div class="mb-4">
                <label for="text" class="form-label">Contenu</label>
                <textarea class="form-control" id="text" name="text" rows="6" placeholder="Exprimez-vous..."><?= htmlspecialchars($post['text']); ?></textarea>
            </div>

            <!-- Upload Fichier -->
            <div class="mb-4">
                <label class="form-label">Image ou Vidéo (optionnel)</label>
                <input type="file" class="form-control" id="jointure" name="jointure" accept="image/*,video/*">
                
                <?php if ($post['jointure']): ?>
                    <p>Fichier actuel: <?= $post['jointure']; ?></p>
                <?php endif; ?>
            </div>

            <!-- Submit Button -->
            <div class="d-grid mt-5">
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-paper-plane me-2"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</body>
</html>
