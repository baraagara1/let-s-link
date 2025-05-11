<?php
require_once '../../../config.php'; // adapte si besoin

try {
    $db = config::getConnexion();

    $email = "baraagara.ts@gmail.com";
    $motDePasseClair = "test123"; // Ton mot de passe visible
    $motDePasseHash = password_hash($motDePasseClair, PASSWORD_DEFAULT); // On le crypte

    $stmt = $db->prepare("INSERT INTO usser (email, mot) VALUES (?, ?)");
    $stmt->execute([$email, $motDePasseHash]);

    echo "✅ Utilisateur créé : $email / test123";
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
