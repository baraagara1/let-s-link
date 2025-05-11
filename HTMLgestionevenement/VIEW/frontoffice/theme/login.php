<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4">
                <h2 class="mb-4 text-center">Connexion</h2>

                <?php if (isset($_SESSION['erreur'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
                <?php endif; ?>

                <form method="POST" action="login_check.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="mb-4">
                        <label for="mot" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" name="mot" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>

                <p class="text-center mt-3 text-muted">Pas encore inscrit ? <a href="#">Créer un compte</a></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
