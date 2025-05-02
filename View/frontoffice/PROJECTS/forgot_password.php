<?php
require_once 'C:\xampp\htdocs\mon_project_web\config.php';
require_once 'C:\xampp\htdocs\mon_project_web\controller\utilisateurC.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $utilisateurC = new UtilisateurC();
    $resetCode = $utilisateurC->generateResetCodeForEmail($email);

    if ($resetCode) {
        // ✅ Send email logic (or redirect to confirmation)
        echo "Lien de réinitialisation généré avec succès.";
        // Optionally, send an email here using mail() or PHPMailer
    } else {
        echo "Adresse email introuvable.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récupération de mot de passe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mot de passe oublié</h1>
        <p>Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
        
        <form action="send_reset_link.php" method="POST">
    <input type="email" id="email" name="email" required>
    <button type="submit">Envoyer le lien de réinitialisation</button>
</form>
        
        <div id="successMessage" class="message success">
            Un email avec les instructions de réinitialisation a été envoyé à votre adresse.
        </div>
        
        <div id="errorMessage" class="message error">
            Une erreur s'est produite. Veuillez vérifier votre adresse email.
        </div>
        
        <div class="login-link">
            <a href="login.php ">Retour à la page de connexion</a>
        </div>
    </div>

    <script>
        document.getElementById('passwordResetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Ici, vous devriez ajouter la logique pour envoyer la demande de réinitialisation
            // Par exemple, une requête AJAX à votre serveur
            
            // Simulation d'envoi réussi (à remplacer par votre logique réelle)
            const email = document.getElementById('email').value;
            console.log('Demande de réinitialisation pour:', email);
            
            // Afficher le message de succès (cacher l'erreur si elle était visible)
            document.getElementById('successMessage').style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
            
            // Vous pouvez aussi vider le champ email si nécessaire
            // document.getElementById('email').value = '';
            
            // En production, vous devriez gérer les erreurs et afficher le message d'erreur si nécessaire
        });
    </script>
</body>
</html>
