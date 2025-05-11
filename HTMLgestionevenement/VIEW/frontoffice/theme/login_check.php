<?php
session_start();
require_once '../../../config.php'; // Mets le bon chemin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['mot'] ?? '';

    try {
        $db = config::getConnexion();

        $stmt = $db->prepare("SELECT id, mot FROM usser WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot'])) {
            $_SESSION['id'] = $user['id'];
            header("Location: project.php?id=" . $user['id']);
            exit;
        } else {
            $_SESSION['erreur'] = "Email ou mot de passe incorrect.";
            header("Location: login.php");
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['erreur'] = "Erreur serveur : " . $e->getMessage();
        header("Location: login.php");
        exit;
    }
}
?>
