<?php
require_once 'models/Reservation.php';

class ReservationController
{
    // 🔹 Ajouter une réservation
    public function reserver()
{
    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'covoiturage_id' => $_POST['covoiturage_id'] ?? '',
            'user_id' => $_POST['user_id'] ?? '',
            'nb_place_res' => $_POST['nb_place_res'] ?? '',
            'moyen_paiement' => $_POST['moyen_paiement'] ?? '',
            'status' => $_POST['status'] ?? 'En attente'
        ];

        $errors = [];

        // Connexion PDO
        $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 🔍 Récupérer les places disponibles
        $stmt = $pdo->prepare("SELECT place_dispo FROM covoiturage WHERE id_cov = ?");
        $stmt->execute([$data['covoiturage_id']]);
        $placesDisponibles = (int) $stmt->fetchColumn();

        // ✅ CONTRÔLES
        if (!preg_match('/^\d{6}$/', $data['user_id'])) {
            $errors['user_id'] = "L'ID utilisateur doit contenir exactement 6 chiffres.";
        }

        if (!is_numeric($data['nb_place_res']) || $data['nb_place_res'] < 1) {
            $errors['nb_place_res'] = "Le nombre de places doit être > 0.";
        } elseif ($data['nb_place_res'] > $placesDisponibles) {
            $errors['nb_place_res'] = "❌ Il ne reste que $placesDisponibles place(s) disponibles.";
        }

        if (empty($data['moyen_paiement'])) {
            $errors['moyen_paiement'] = "Choisissez un mode de paiement.";
        }

        if (empty($data['status'])) {
            $errors['status'] = "Statut obligatoire.";
        }

        // 🚫 S'il y a des erreurs
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $data;
            header("Location: index.php?action=lister&reserver=" . $data['covoiturage_id']);
            exit;
        }

        // ✅ Sinon, enregistrer la réservation
        $reservation = new Reservation(
            $data['covoiturage_id'],
            $data['user_id'],
            $data['nb_place_res'],
            $data['moyen_paiement'],
            $data['status']
        );
        $reservation->save();

        // 🔄 Mettre à jour les places disponibles
        $updateStmt = $pdo->prepare("UPDATE covoiturage SET place_dispo = place_dispo - ? WHERE id_cov = ?");
        $updateStmt->execute([$data['nb_place_res'], $data['covoiturage_id']]);

        header("Location: index.php?action=lister&success=1");
        exit;
    } else {
        echo "❌ Méthode non autorisée.";
    }
}

    // 🔹 Modifier une réservation
    public function traiterModification()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_res = $_POST['id_res'];
            $user_id = $_POST['user_id'];
            $nb_place_res = $_POST['nb_place_res'];
            $moyen_paiement = $_POST['moyen_paiement'];
            $status = $_POST['status'];

            try {
                $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // 🟠 Récupérer l'ancienne réservation
                $stmtOld = $pdo->prepare("SELECT nb_place_res, covoiturage_id FROM reservations WHERE id_res = ?");
                $stmtOld->execute([$id_res]);
                $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

                $ancienne_place = $old['nb_place_res'];
                $covoiturage_id = $old['covoiturage_id'];

                // 🟠 Calculer la différence
                $diff = $ancienne_place - $nb_place_res;

                // 🟠 Mettre à jour les places disponibles
                $stmtUpdate = $pdo->prepare("UPDATE covoiturage SET place_dispo = place_dispo + ? WHERE id_cov = ?");
                $stmtUpdate->execute([$diff, $covoiturage_id]);

                // ✅ Mise à jour de la réservation
                $stmt = $pdo->prepare("UPDATE reservations SET user_id = ?, nb_place_res = ?, moyen_paiement = ?, status = ? WHERE id_res = ?");
                $stmt->execute([$user_id, $nb_place_res, $moyen_paiement, $status, $id_res]);

                header("Location: index.php?action=lister");
                exit;
            } catch (PDOException $e) {
                die("❌ Erreur de modification : " . $e->getMessage());
            }
        }
    }


    // controllers/ReservationController.php

public function notifierAdmin() {
    if (isset($_GET['id_res']) && isset($_GET['user_id'])) {
        $id_res = intval($_GET['id_res']);
        $user_id = intval($_GET['user_id']);

        try {
            $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Vérifie si la réservation existe
            $stmt = $pdo->prepare("SELECT id_res FROM reservations WHERE id_res = ?");
            $stmt->execute([$id_res]);
            if (!$stmt->fetch()) {
                exit("❌ Réservation introuvable.");
            }

            // Vérifie si l'utilisateur existe
            $stmt = $pdo->prepare("SELECT id FROM usser WHERE id = ?");
            $stmt->execute([$user_id]);
            if (!$stmt->fetch()) {
                exit("❌ Utilisateur introuvable.");
            }

            // Vérifie s'il a déjà notifié
            $stmt = $pdo->prepare("SELECT * FROM demandes_suppression WHERE reservation_id = ? AND user_id = ?");
            $stmt->execute([$id_res, $user_id]);
            if ($stmt->rowCount() > 0) {
                exit("⚠️ Vous avez déjà envoyé une demande.");
            }

            // Insertion de la demande
            $stmt = $pdo->prepare("INSERT INTO demandes_suppression (reservation_id, user_id, statut, date_demande) VALUES (?, ?, 'En attente', NOW())");
            $stmt->execute([$id_res, $user_id]);

            header("Location: index.php?action=lister&notification=envoyee");
            exit;

        } catch (PDOException $e) {
            exit("❌ Erreur DB : " . $e->getMessage());
        }
    } else {
        exit("❌ Paramètres manquants.");
    }
}
public function supprimer()
{
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        try {
            $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 🔍 Récupérer le nombre de places à libérer et le covoiturage
            $stmt = $pdo->prepare("SELECT covoiturage_id, nb_place_res FROM reservations WHERE id_res = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($res) {
                $covoiturage_id = $res['covoiturage_id'];
                $places = $res['nb_place_res'];

                // 🔁 Supprimer la réservation
                $deleteStmt = $pdo->prepare("DELETE FROM reservations WHERE id_res = ?");
                $deleteStmt->execute([$id]);

                // 🔄 Ré-incrémenter les places disponibles
                $updateStmt = $pdo->prepare("UPDATE covoiturage SET place_dispo = place_dispo + ? WHERE id_cov = ?");
                $updateStmt->execute([$places, $covoiturage_id]);

                header("Location: index.php?action=lister");
                exit;
            } else {
                echo "❌ Réservation introuvable.";
            }
        } catch (PDOException $e) {
            die("❌ Erreur : " . $e->getMessage());
        }
    } else {
        echo "❌ ID manquant.";
    }
}





}
