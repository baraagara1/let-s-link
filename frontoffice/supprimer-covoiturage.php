<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    http_response_code(403);
    exit("❌ Accès interdit. Veuillez vous connecter.");
}

if (isset($_GET['id'])) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $id_cov = intval($_GET['id']);
        $id_utilisateur = $_SESSION['utilisateur_id'];

        // Vérifier que le covoiturage appartient à l'utilisateur connecté
        $verif = $pdo->prepare("SELECT COUNT(*) FROM covoiturage WHERE id_cov = ? AND id_utilisateur = ?");
        $verif->execute([$id_cov, $id_utilisateur]);

        if ($verif->fetchColumn() == 0) {
            exit("❌ Ce covoiturage ne vous appartient pas.");
        }

        // Supprimer le covoiturage
        $stmt = $pdo->prepare("DELETE FROM covoiturage WHERE id_cov = ?");
        $stmt->execute([$id_cov]);

        header("Location: lister-covoiturages.php?deleted=1");
        exit;

    } catch (PDOException $e) {
        die("❌ Erreur de suppression : " . $e->getMessage());
    }
} else {
    echo "❗ Aucun ID spécifié pour la suppression.";
}
