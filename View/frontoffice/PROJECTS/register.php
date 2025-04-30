<?php
require_once 'C:\xampp\htdocs\mon_project_web\model\utilisateur.php';
require_once 'C:\xampp\htdocs\mon_project_web\controller\utilisateurC.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['last-name']) && isset($_POST['first-name']) && isset($_POST['email']) &&
        isset($_POST['phone']) && isset($_POST['address']) && isset($_POST['password']) &&
        isset($_POST['role'])
    ) {
        $nom = trim($_POST['last-name']);
        $prenom = trim($_POST['first-name']);
        $email = trim($_POST['email']);
        $telephone = intval($_POST['phone']);
        $adresse = trim($_POST['address']);
        $motpasse = $_POST['password'];
        $role = $_POST['role'];

        $utilisateur = new Utilisateur($nom, $prenom, $email, $telephone, $adresse, $motpasse, $role);
        $utilisateurC = new UtilisateurC();

        try {
            $utilisateurC->ajouterUtilisateur($utilisateur);
            $message = "✅ Utilisateur ajouté avec succès.";
            header("Location: index.html"); // Redirection après ajout
            exit;
        } catch (Exception $e) {
            $message = "❌ Erreur lors de l'ajout : " . $e->getMessage();
        }
    } else {
        $message = "❌ Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin: 6px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .submit-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-bottom: 10px;
            color: red;
        }
    </style>
</head>
<body>

<form method="POST" action="">
    <h2>Inscription</h2>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <label for="last-name">Nom</label>
    <input type="text" id="last-name" name="last-name" required>

    <label for="first-name">Prénom</label>
    <input type="text" id="first-name" name="first-name" required>

    <label for="phone">Téléphone</label>
    <input type="tel" id="phone" name="phone" pattern="[0-9]{8,15}" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" required minlength="6">

    <label for="address">Adresse</label>
    <input type="text" id="address" name="address" required>

    <label for="role">Rôle</label>
    <input type="text" id="role" name="role" value="utilisateur" readonly>

    <input type="submit" class="submit-btn" value="S'inscrire">
</form>

</body>
</html>
