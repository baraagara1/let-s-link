<?php
require_once 'C:\xampp\htdocs\mon_project_web\config.php';
require_once 'C:\xampp\htdocs\mon_project_web\controller\utilisateurC.php';

$utilisateurC = new UtilisateurC();

// Vérification de l'email et génération du code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $result = $utilisateurC->verifyEmailAndGenerateCode($email);

    if ($result) {
        // Stocker l'email en session pour la vérification ultérieure
        session_start();
        $_SESSION['reset_email'] = $email;
        echo "<script>alert('Code de réinitialisation envoyé.');</script>";
    } else {
        echo "<script>alert('Cette adresse email n\'existe pas.'); window.history.back();</script>";
        exit();
    }
}

// Vérification du code saisi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    session_start();
    $email = $_SESSION['reset_email'] ?? '';
    $enteredCode = $_POST['code'];
    
    if ($utilisateurC->verifyResetCode($email, $enteredCode)) {
        // Code valide, rediriger vers la page de réinitialisation
        header("Location: reset_password.php");
        exit();
    } else {
        echo "<script>alert('Code incorrect. Veuillez réessayer.');</script>";
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
        
        <form method="POST" action="">
            <div class="code-input">
                <input type="text" name="code1" maxlength="1" required>
                <input type="text" name="code2" maxlength="1" required>
                <input type="text" name="code3" maxlength="1" required>
                <input type="text" name="code4" maxlength="1" required>
                <input type="text" name="code5" maxlength="1" required>
            </div>
            <input type="hidden" name="code" id="fullCode">
            <button type="button" onclick="submitCode()">Vérifier le code</button>
        </form>
        
        <div class="resend">
            <p>Vous n'avez pas reçu de code? <a href="javascript:resendCode()">Renvoyer le code</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.code-input input');
            
            inputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    updateFullCode();
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });
        });

        function updateFullCode() {
            const codes = Array.from(document.querySelectorAll('.code-input input'))
                              .map(input => input.value)
                              .join('');
            document.getElementById('fullCode').value = codes;
        }

        function submitCode() {
            updateFullCode();
            document.forms[0].submit();
        }

        function resendCode() {
            // Vous pouvez implémenter ici la logique pour renvoyer le code
            alert('Un nouveau code a été envoyé à votre adresse email.');
        }
    </script>
</body>
</html>