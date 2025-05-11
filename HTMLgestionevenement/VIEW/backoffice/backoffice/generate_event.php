<?php
require_once '../../../config.php';
require_once '../../../model/Event.php';
require_once '../../../controller/EventController.php';
require_once '../../../controller/ActiviteController.php';

header('Content-Type: application/json');

if (!isset($_GET['id_a'])) {
    echo json_encode(['success' => false, 'message' => "Paramètre 'id_a' manquant."]);
    exit;
}

$id_a = intval($_GET['id_a']);
//ici api
$apiKey = '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';
// Récupérer les anciens événements
$evenementsExistants = (new EventController())->getEventsByActivite($id_a);
$titres = array_map(fn($e) => $e['nom_e'], $evenementsExistants);
$lieux = array_map(fn($e) => $e['lieu_e'], $evenementsExistants);

$titresStr = implode(", ", array_unique($titres));
$lieuxStr = implode(", ", array_unique($lieux));
$today = date("Y-m-d");
$limitDate = date("Y-m-d", strtotime("+30 days"));

// Prompt intelligent
$prompt = "Nous sommes le $today.
L'activité sélectionnée possède déjà ces événements : $titresStr.
Ils ont eu lieu dans ces lieux en Tunisie : $lieuxStr.

Génère 2 nouveaux événements cohérents avec les précédents.
Utilise les mêmes lieux ou des lieux populaires et amusants en Tunisie.
Les dates doivent être entre aujourd'hui et le $limitDate (format YYYY-MM-DD).
Réponds uniquement avec un tableau JSON contenant les clés exactes : titre, lieu, date.";

$data = [
    'model' => 'gpt-4o',
    'messages' => [
        ['role' => 'system', 'content' => 'Tu es un assistant qui génère des événements culturels pour un site tunisien.'],
        ['role' => 'user', 'content' => $prompt],
    ],
    'temperature' => 0.7,
];

// Requête GPT
$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode(['success' => false, 'error' => 'Erreur cURL']);
    exit;
}

$result = json_decode($response, true);
if (!isset($result['choices'][0]['message']['content'])) {
    echo json_encode(['success' => false, 'error' => 'Réponse incomplète', 'debug' => $result]);
    exit;
}

$content = trim(preg_replace('/^```json|```$/i', '', $result['choices'][0]['message']['content']));
$eventsJson = json_decode($content, true);

if (!is_array($eventsJson)) {
    echo json_encode(['success' => false, 'error' => 'Format JSON invalide', 'content' => $content]);
    exit;
}

// 🔧 Fonction image DALL·E
function generateImageURL($titre, $apiKey) {
    $imagePrompt = "Affiche artistique réaliste pour un événement intitulé \"$titre\", ambiance tunisienne, jeunes, dynamique, couleurs vives, style professionnel.";
    $imageData = ['prompt' => $imagePrompt, 'n' => 1, 'size' => "512x512"];

    $ch = curl_init("https://api.openai.com/v1/images/generations");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($imageData));

    $imgResponse = curl_exec($ch);
    curl_close($ch);

    $imgResult = json_decode($imgResponse, true);
    return $imgResult['data'][0]['url'] ?? null;
}

// 🔁 Insertion BDD + image
$eventController = new EventController();
$inserted = 0;

foreach ($eventsJson as $eventData) {
    if (!isset($eventData['titre'], $eventData['lieu'], $eventData['date'])) continue;

    $imageUrl = generateImageURL($eventData['titre'], $apiKey);
    $imageFile = "default.jpg";

    if ($imageUrl) {
        $imageContent = file_get_contents($imageUrl);
        $filename = "event_" . uniqid() . ".jpg";
        file_put_contents("../../uploads/" . $filename, $imageContent);
        $imageFile = $filename;
    }

    $event = new Event(
        $eventData['titre'],
        $id_a,
        $eventData['date'],
        $eventData['lieu'],
        $imageFile
    );

    $eventController->addEvent($event);
    $inserted++;
}

echo json_encode(['success' => true, 'message' => "$inserted événements insérés avec succès."]);
