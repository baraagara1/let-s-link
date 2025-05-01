<?php
require_once '../../../controller/CommentaireController.php';
require_once '../../../MODEL/Commentaire.php';

$successMessage = '';
$errorMessage = '';

// Si l'ID du commentaire est présent dans l'URL, récupérer l'information
if (isset($_GET['id'])) {
    $controller = new CommentaireController();
    $comment = $controller->getCommentaireById($_GET['id']);  // Récupérer le commentaire par son ID
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_c = $_POST['id_c'];  // L'ID du commentaire reste caché mais est envoyé
    $contenu = $_POST['contenu'];

    // Mettre à jour le commentaire
    $controller = new CommentaireController();
    if ($controller->updateCommentaire($contenu, $id_c)) {
        $successMessage = "Commentaire mis à jour avec succès!";
        // Redirection vers la page des commentaires après 2 secondes
        header("Refresh: 2; URL=commentaire.php");
    } else {
        $errorMessage = "Erreur lors de la mise à jour du commentaire.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Commentaire - Let's Link</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h2>Modifier le Commentaire</h2>

        <!-- Affichage des messages de succès ou d'erreur -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= $successMessage; ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="alert alert-danger"><?= $errorMessage; ?></div>
        <?php endif; ?>

        <form action="modifier_commentaire.php?id=<?= $comment['id_c']; ?>" method="POST">
            <input type="hidden" name="id_c" value="<?= $comment['id_c']; ?>">

            <!-- Contenu du commentaire -->
            <div class="mb-4">
                <label for="contenu" class="form-label">Contenu</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="6"><?= htmlspecialchars($comment['contenu']); ?></textarea>
                </div>

            <!-- Submit Button -->
            <div class="d-grid mt-5">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</body>
</html>
