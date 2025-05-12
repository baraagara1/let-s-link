<?php
session_start();

// Redirige si déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM usser WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Authentification simple (mot de passe en clair pour l’instant)
        if ($user && $mdp === $user['mot']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            header("Location: index.php");
            exit;
        } else {
            $erreur = "❌ Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $erreur = "Erreur PDO : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #80deea);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 400px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">Connexion</h3>

    <?php if (!empty($erreur)): ?>
        <div class="alert alert-danger"><?= $erreur ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" class="form-control" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </div>
    </form>
</div>

</body>
</html>
