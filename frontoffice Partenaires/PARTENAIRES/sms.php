<?php
// 1) Active l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Charge l'autoloader généré par Composer (ajuste le chemin si nécessaire)
require_once __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;

// 3) Tes identifiants Twilio (remplace [AuthToken] par ton véritable token)
$sid    = 'AC637027dcd605e7150c5d1c58a3edc95a';
$token  = 'e82ff711a6fa2a36fb51e3431e331c10';

// 4) Numéros :  
//   - $from doit être un numéro Twilio actif (acheté dans ta console)  
//   - $to est le destinataire (numéro de ton utilisateur)  
$from = '+18564167092';   // Exemple : ton numéro Twilio  
$to   = '+21651906215';   // Exemple : un numéro tunisien valide  

// 5) Instanciation du client
$client = new Client($sid, $token);

try {
    // 6) Envoi du SMS
    $message = $client->messages->create(
        $to,
        [
            'from' => $from,
            'body' => 'Bonjour ! Voici un test d’envoi SMS depuis Twilio + PHP.' 
        ]
    );

    // 7) Affiche le SID du message en retour pour confirmer
    echo "✅ SMS envoyé ! SID = " . $message->sid . "\n";

} catch (Exception $e) {
    // En cas d’erreur, affiche-la
    echo "❌ Erreur Twilio : " . $e->getMessage() . "\n";
}
