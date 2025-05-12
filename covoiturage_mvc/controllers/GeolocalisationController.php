<?php

class GeolocalisationController {

    // 🔁 Enregistre la position du conducteur
    public function updatePosition() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cov = $_POST['id_cov'];
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];

            try {
                $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare("UPDATE covoiturage SET latitude = ?, longitude = ? WHERE id_cov = ?");
                $stmt->execute([$latitude, $longitude, $id_cov]);

                echo json_encode(['status' => 'success']);
            } catch (PDOException $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    // 🔍 Récupère la position pour l'affichage passager
    public function getPosition() {
        if (isset($_GET['id_cov'])) {
            $id_cov = $_GET['id_cov'];

            try {
                $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare("SELECT latitude, longitude FROM covoiturage WHERE id_cov = ?");
                $stmt->execute([$id_cov]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);

                echo json_encode($data);
            } catch (PDOException $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    // ✅ Active le partage
    public function activerPartage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['demarrer_partage'])) {
            $idCov = intval($_POST['demarrer_partage']);

            $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("UPDATE covoiturage SET partage_actif = 1 WHERE id_cov = ?");
            $stmt->execute([$idCov]);

            header("Location: index.php?action=lister");
            exit;
        }
    }
}
