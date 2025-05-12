<?php
class AuthController {
    public function login() {
        include 'views/auth/login.php';
    }

    public function verifierLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $mot_de_passe = $_POST['mot'] ?? ''; // ✅ champ correct

            try {
                $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // ✅ table 'usser' et champ 'mot'
                $stmt = $pdo->prepare("SELECT * FROM usser WHERE email = ? AND mot = ?");
                $stmt->execute([$email, $mot_de_passe]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    session_start();
                    $_SESSION['user_id'] = $user['id']; // ✅ champ correct
                    header("Location: index.php?action=liste");
                    exit;
                } else {
                    header("Location: index.php?action=login&erreur=1");
                    exit;
                }

            } catch (PDOException $e) {
                die("❌ Erreur PDO : " . $e->getMessage());
            }
        }
    }
}
