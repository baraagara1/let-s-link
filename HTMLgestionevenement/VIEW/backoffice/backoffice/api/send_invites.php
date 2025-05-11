<?php
require_once '../../../../config.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = json_decode(file_get_contents("php://input"), true);

    if (!is_array($ids) || empty($ids)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Liste d'IDs invalide."]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $db = config::getConnexion();
    $query = $db->prepare("SELECT email, nom FROM utilisateur WHERE id_u IN ($placeholders)");
    $query->execute($ids);
    $users = $query->fetchAll(PDO::FETCH_ASSOC);

    $success = [];
    $fail = [];

    foreach ($users as $user) {
        $mail = new PHPMailer(true);

        try {
            // ✅ Configuration SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ttnomame@gmail.com'; // 🔁 ton adresse Gmail
            $mail->Password = 'codldgnbswxzrxju';   // 🔁 mot de passe d'application
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // ✅ Email
            $mail->setFrom('ttnomame@gmail.com', 'Gestion Événements');
            $mail->addAddress($user['email'], $user['nom']);
            $mail->isHTML(true);
            $mail->Subject = '🎉 Invitation à un événement';
            $mail->Body = "
                Bonjour {$user['nom']},<br><br>
                Vous êtes invité(e) à un événement spécial !<br><br>
                Cordialement,<br>L'équipe.
            ";

            $mail->send();
            $success[] = $user['email'];
        } catch (Exception $e) {
            $fail[] = $user['email'];
        }
    }

    echo json_encode([
        "envoyes" => $success,
        "echoues" => $fail
    ]);
}
