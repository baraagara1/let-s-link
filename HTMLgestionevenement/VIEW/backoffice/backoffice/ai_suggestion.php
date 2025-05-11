<?php
header('Content-Type: application/json');
require_once '../../../controller/EventController.php';

$id_e = $_GET['id_e'] ?? null;
if (!$id_e) {
    echo json_encode([]);
    exit;
}

$eventController = new EventController();
$suggested = $eventController->suggestUsersByAIWithoutUserController($id_e);

echo json_encode($suggested);
exit;
