<?php
if (!isset($_GET['id_cov'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'id_cov requis']));
}

$id_cov = intval($_GET['id_cov']);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 🔒 Vérifie d'abord que le partage est actif
    $check = $pdo->prepare("SELECT partage_actif FROM covoiturage WHERE id_cov = ?");
    $check->execute([$id_cov]);
    $partageActif = $check->fetchColumn();

    if ($partageActif != 1) {
        http_response_code(403);
        exit(json_encode(['error' => 'Partage non autorisé']));
    }

    // ✅ Ensuite, récupère la position
    $stmt = $pdo->prepare("SELECT latitude, longitude FROM covoiturage WHERE id_cov = ?");
    $stmt->execute([$id_cov]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result ?: ['latitude' => null, 'longitude' => null]);

} catch (PDOException $e) {
    http_response_code(500);
    exit(json_encode(['error' => $e->getMessage()]));
}
