<?php
class SignalementController {
    public function ajouter() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $id_cov = intval($_POST['id_cov']);
            $message = trim($_POST['message']);
            $user_id = $_SESSION['user_id'];

            $stmt = $pdo->prepare("INSERT INTO signalements (id_cov, user_id, message, date_signalement) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$id_cov, $user_id, $message]);

            header("Location: index.php?action=lister&signalement=ok");
            exit;
        }
    }
}
