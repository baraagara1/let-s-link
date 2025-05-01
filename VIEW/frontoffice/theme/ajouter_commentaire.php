<?php
require_once '../../../controller/CommentaireController.php';
header('Content-Type: application/json');

function detectGrosMotsAvecOpenAI($texte) {
   
    $prompt = "Ce texte contient-il des gros mots ? Réponds seulement par oui ou non. Texte : \"$texte\"";
    $data = [
        'model' => 'gpt-4o',
        'messages' => [
            ['role' => 'system', 'content' => 'Tu es un modérateur de texte.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0
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

    $result = json_decode($response, true);
    $content = strtolower(trim($result['choices'][0]['message']['content'] ?? ''));
    return (strpos($content, 'oui') !== false);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = $_POST['contenu'] ?? '';
    $id_p = $_POST['id_post'] ?? 0;
    $id_u = 1;

    if (trim($contenu) === '') {
        echo json_encode(['success' => false, 'message' => 'Commentaire vide']);
        exit;
    }

    if (detectGrosMotsAvecOpenAI($contenu)) {
        echo json_encode(['success' => false, 'message' => '⚠️ Gros mots détectés']);
        exit;
    }

    $controller = new CommentaireController();
    $controller->addCommentaire($contenu, $id_p, $id_u);
    echo json_encode(['success' => true, 'message' => 'Commentaire ajouté']);
}
?>
