<?php
// Paramètres de connexion à la base
$host = 'localhost';
$dbname = 'covoiturage_db';
$user = 'root';
$password = '';

try {
    // Connexion à la base avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifie que l'id_cov est bien reçu en GET
if (isset($_GET['id_cov']) && is_numeric($_GET['id_cov'])) {
    $id_cov = (int) $_GET['id_cov'];

    try {
        // Prépare et exécute la requête SQL de suppression
        $stmt = $pdo->prepare("DELETE FROM covoiturage WHERE id_cov = :id_cov");
        $stmt->execute([':id_cov' => $id_cov]);

        // Redirection si succès
        header("Location: covoituragelist.php?msg=supprime");
        exit();
    } catch (PDOException $e) {
        echo "❌ Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    echo "⚠️ Identifiant invalide ou non spécifié.";
}
?>
