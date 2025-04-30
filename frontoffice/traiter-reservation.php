<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Initialisation
        $erreurs = [];
        $utilisateur_id = trim($_POST['utilisateur_id'] ?? '');
        $covoiturage_id = trim($_POST['covoiturage_id'] ?? '');
        $moyen_paiement = trim($_POST['moyen_paiement'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $nb_place_res = trim($_POST['nb_place_res'] ?? '');

        // Vérifications PHP
        if (!preg_match('/^\d{6}$/', $utilisateur_id)) {
            $erreurs[] = 'id_invalide';
        }

        if (!is_numeric($covoiturage_id) || intval($covoiturage_id) <= 0) {
            $erreurs[] = 'covoiturage_invalide';
        }

        if (!in_array($moyen_paiement, ['Espèces', 'Carte Bancaire', 'Virement'])) {
            $erreurs[] = 'paiement_invalide';
        }

        if ($status !== 'En attente') {
            $erreurs[] = 'statut_invalide';
        }

        if (!is_numeric($nb_place_res) || intval($nb_place_res) < 1) {
            $erreurs[] = 'nb_places_invalide';
        }

        // Vérification places disponibles
        if (empty($erreurs)) {
            $check = $pdo->prepare("SELECT place_dispo FROM covoiturage WHERE id_cov = ?");
            $check->execute([intval($covoiturage_id)]);
            $places_dispo = $check->fetchColumn();

            if ($places_dispo === false) {
                $erreurs[] = 'covoiturage_introuvable';
            } elseif ($places_dispo < intval($nb_place_res)) {
                $erreurs[] = 'pas_assez_de_places';
            }
        }

        // Redirection en cas d'erreurs
        if (!empty($erreurs)) {
            $query = http_build_query([
                'reserver' => $covoiturage_id,
                'erreur' => implode(',', $erreurs)
            ]);
            header("Location: lister-covoiturages.php?$query");
            exit;
        }

        // Insertion réservation
        $stmt = $pdo->prepare("INSERT INTO reservations (moyen_paiement, utilisateur_id, covoiturage_id, status, nb_place_res)
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $moyen_paiement,
            intval($utilisateur_id),
            intval($covoiturage_id),
            $status,
            intval($nb_place_res)
        ]);

        // Mise à jour du nombre de places restantes
        $pdo->prepare("UPDATE covoiturage SET place_dispo = place_dispo - ? WHERE id_cov = ?")
            ->execute([intval($nb_place_res), intval($covoiturage_id)]);

        // Redirection finale
        header("Location: lister-covoiturages.php?reservations=ok");
        exit;
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
