<?php
session_start();
require_once '../../../config.php';
require_once '../../../controller/ActiviteController.php';

header('Content-Type: application/json');

// Simule un ID utilisateur si non connecté
$id_u = $_SESSION['id_u'] ?? 1;
$message = trim($_POST['message'] ?? '');

// API OpenAI


// Initialisation historique
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [
        ['role' => 'system', 'content' => "Tu es un assistant IA sympa. Ta mission est d'aider une personne à découvrir une activité qui lui plaît. Pose une ou deux questions naturelles comme 'Tu es plutôt sport ou détente ?' et ensuite, propose des activités précises tirées d'une base de données."]
    ];
}

// Ajout du message utilisateur
$_SESSION['chat_history'][] = ['role' => 'user', 'content' => $message];

// Préparation de la requête OpenAI
$data = [
    'model' => 'gpt-4o',
    'messages' => $_SESSION['chat_history'],
    'temperature' => 0.7
];

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

// Traitement de la réponse
$reply = "Je n’ai pas compris, peux-tu reformuler ?";
if ($response) {
    $result = json_decode($response, true);
    $reply = $result['choices'][0]['message']['content'] ?? $reply;
    $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $reply];
}

// Vérifie si l'utilisateur a assez répondu → alors on recommande
$questionCount = 0;
foreach ($_SESSION['chat_history'] as $msg) {
    if ($msg['role'] === 'assistant' && str_ends_with(trim($msg['content']), '?')) {
        $questionCount++;
    }
}

// Recherche d’activités seulement après 2 échanges
if ($questionCount >= 2) {
    $activiteController = new ActiviteController();
    $keywords = ['sport', 'musique', 'art', 'lecture', 'cinéma', 'plein air', 'ville', 'jeux', 'tech', 'danse'];
    $matchedActivities = [];

    foreach ($keywords as $keyword) {
        if (stripos($message, $keyword) !== false || stripos($reply, $keyword) !== false) {
            $matchedActivities = $activiteController->searchActivitesByKeyword($keyword);
            break;
        }
    }

    if (!empty($matchedActivities)) {
        $reply .= "<br><br>🎯 Voici des activités qui pourraient te plaire :<ul>";
        foreach ($matchedActivities as $act) {
            $reply .= "<li><strong>" . htmlspecialchars($act['nom_a']) . "</strong></li>";
        }
        $reply .= "</ul>";

        // Reset de la conversation pour un nouveau utilisateur
        unset($_SESSION['chat_history']);
    }
}

echo json_encode(['reply' => $reply]);
