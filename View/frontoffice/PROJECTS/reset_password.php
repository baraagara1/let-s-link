<?php
require_once 'C:\xampp\htdocs\mon_project_web\config.php';
require_once 'C:\xampp\htdocs\mon_project_web\controller\utilisateurC.php';

session_start();
$userC = new UtilisateurC();
$message = '';
$showPasswordForm = false;

// Vérifier si l'email est en session (venant de la page de demande de réinitialisation)
if (isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
    $showPasswordForm = true;
}

// Traitement du formulaire de nouveau mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("UPDATE usser SET mot = :password WHERE email = :email");
            $stmt->execute([
                'password' => $newPassword, // ⚠ En production, utiliser password_hash()
                'email' => $email
            ]);
            
            $message = "Votre mot de passe a été réinitialisé avec succès.";
            unset($_SESSION['reset_email']);
            $showPasswordForm = false;
        } catch (PDOException $e) {
            $message = "Une erreur est survenue lors de la réinitialisation : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4bb543;
            --error-color: #ff3333;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .reset-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            transition: all 0.3s ease;
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .reset-header h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .reset-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--dark-color);
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .btn {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
        }
        
        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        
        .success {
            background-color: rgba(75, 181, 67, 0.2);
            color: var(--success-color);
        }
        
        .error {
            background-color: rgba(255, 51, 51, 0.2);
            color: var(--error-color);
        }
        
        .password-strength {
            margin-top: 5px;
            height: 4px;
            background-color: #eee;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background-color 0.3s;
        }
        
        @media (max-width: 576px) {
            .reset-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h1>Réinitialiser votre mot de passe</h1>
            <p>Entrez votre nouveau mot de passe ci-dessous</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($showPasswordForm): ?>
            <form action="reset_password.php" method="POST" id="resetForm">
                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required 
                           minlength="8" placeholder="Au moins 8 caractères">
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required 
                           placeholder="Retapez votre mot de passe">
                </div>
                
                <button type="submit" class="btn">Réinitialiser le mot de passe</button>
            </form>
        <?php else: ?>
            <div class="message">
                <p>Vous ne pouvez accéder à cette page directement. Veuillez suivre le lien envoyé par email.</p>
                <p><a href="login.php">Retour à la page de connexion</a></p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Script pour afficher la force du mot de passe
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            let strength = 0;
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
            
            // Mettre à jour la barre de force
            const width = strength * 20;
            strengthBar.style.width = width + '%';
            
            // Changer la couleur en fonction de la force
            if (strength <= 2) {
                strengthBar.style.backgroundColor = '#ff3333'; // Faible
            } else if (strength === 3) {
                strengthBar.style.backgroundColor = '#ffcc00'; // Moyen
            } else {
                strengthBar.style.backgroundColor = '#4bb543'; // Fort
            }
        });
        
        // Validation du formulaire
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
            }
        });
    </script>
</body>
</html>