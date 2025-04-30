<?php
$pdo = new PDO('mysql:host=localhost;dbname=covoiturage_db;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id_cov'])) {
    $id_cov = intval($_GET['id_cov']);
    $stmt = $pdo->prepare("SELECT latitude, longitude FROM covoiturage WHERE id_cov = ?");
    $stmt->execute([$id_cov]);
    $pos = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pos) {
        echo json_encode($pos);
    } else {
        echo json_encode([]);
    }
}
?>
