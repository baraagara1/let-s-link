<?php
$host = 'localhost';
$dbname = 'covoiturage_db';
$user = 'root';
$password = '';

try {
    // Connexion avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}

// Vérification des champs
if (
    isset($_POST['id_cov']) &&
    isset($_POST['destination']) &&
    isset($_POST['date']) &&
    isset($_POST['place_dispo']) &&
    isset($_POST['id_utilisateur'])
) {
    $id = (int)$_POST['id_cov'];
    $dest = $_POST['destination'];
    $date = $_POST['date'];
    $places = (int)$_POST['place_dispo'];
    $id_user = (int)$_POST['id_utilisateur'];

    try {
        $sql = "UPDATE covoiturage 
                SET destination = :destination, date = :date, place_dispo = :places, id_utilisateur = :user 
                WHERE id_cov = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':destination' => $dest,
            ':date' => $date,
            ':places' => $places,
            ':user' => $id_user,
            ':id' => $id
        ]);

        header("Location: covoituragelist.php");
        exit();
    } catch (PDOException $e) {
        echo "❌ Erreur lors de la mise à jour : " . $e->getMessage();
    }
} else {
    echo "⚠️ Tous les champs sont requis.";
}
?>
