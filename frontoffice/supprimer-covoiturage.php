<?php
if (isset($_GET['id'])) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $id = intval($_GET['id']);
        $sql = "DELETE FROM covoiturage WHERE id_cov = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        header("Location: lister-covoiturages.php?deleted=1");
        exit;
    } catch (PDOException $e) {
        die("❌ Erreur de suppression : " . $e->getMessage());
    }
} else {
    echo "❗ Aucun ID spécifié pour la suppression.";
}
