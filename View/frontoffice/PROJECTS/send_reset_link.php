<?php
require_once 'C:\xampp\htdocs\mon_project_web\config.php';
require_once 'C:\xampp\htdocs\mon_project_web\controller\utilisateurC.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $utilisateurC = new UtilisateurC();

    $result = $utilisateurC->verifyEmailAndGenerateCode($email);

    if ($result) {
        echo "<script>alert('Code de réinitialisation généré avec succès.'); window.location.href='send_reset_link.php';</script>";
    } else {
        echo "<script>alert('Cette adresse email n\'existe pas.'); window.history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation du code de vérification</title>
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
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            color: #666;
            margin-bottom: 20px;
            text-align: center;
        }
        .code-input {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .code-input input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .resend {
            text-align: center;
            margin-top: 20px;
        }
        .resend a {
            color: #4CAF50;
            text-decoration: none;
        }
        .resend a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vérification du code</h1>
        <p>Nous avons envoyé un code de vérification à votre adresse email. Veuillez le saisir ci-dessous.</p>
        
        <form action="/verifier-code" method="POST">
            <div class="code-input">
                <input type="text" name="code1" maxlength="1" pattern="[0-9]" required>
                <input type="text" name="code2" maxlength="1" pattern="[0-9]" required>
                <input type="text" name="code3" maxlength="1" pattern="[0-9]" required>
                <input type="text" name="code4" maxlength="1" pattern="[0-9]" required>
                <input type="text" name="code5" maxlength="1" pattern="[0-9]" required>
                
            </div>
            
            <button type="submit">Vérifier le code</button>
        </form>
        
        <div class="resend">
            <p>Vous n'avez pas reçu de code? <a href="/renvoyer-code">Renvoyer le code</a></p>
        </div>
    </div>

    <script>
        // Script pour faciliter la saisie du code
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.code-input input');
            
            inputs.forEach((input, index) => {
                // Passer au champ suivant quand un chiffre est saisi
                input.addEventListener('input', function() {
                    if (this.value.length === 1) {
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }
                });
                
                // Gérer la suppression avec la touche retour
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0) {
                        if (index > 0) {
                            inputs[index - 1].focus();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>