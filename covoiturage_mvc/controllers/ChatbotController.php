<?php

class ChatbotController {
    public function repondre() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $question = strtolower(trim($_POST['question']));
            $reponse = "❌ Je n'ai pas compris votre question.";

            try {
                $pdo = new PDO('mysql:host=localhost;dbname=lets_link;charset=utf8', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                if (strpos($question, 'réserver') !== false) {
                    $reponse = "📝 Pour réserver, cliquez sur le bouton jaune 'Réserver' dans la carte du covoiturage.";
                } else {
                    // 🔍 Extraction des mots clés (min. 3 lettres)
                    $mots = explode(' ', $question);
                    $lieux = [];

                    foreach ($mots as $mot) {
                        if (strlen($mot) >= 3) {
                            $lieux[] = $mot;
                        }
                    }

                    if (!empty($lieux)) {
                        $conditions = [];
                        $params = [];

                        foreach ($lieux as $mot) {
                            $conditions[] = "(lieu_depart LIKE ? OR destination LIKE ?)";
                            $params[] = "%$mot%";
                            $params[] = "%$mot%";
                        }

                        $sql = "SELECT * FROM covoiturage WHERE " . implode(' OR ', $conditions) . " ORDER BY date DESC LIMIT 1";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $cov = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($cov) {
                            $reponse = "✅ Covoiturage correspondant trouvé.";
                            echo json_encode([
                                'reponse' => $reponse,
                                'id_cov' => $cov['id_cov']
                            ]);
                            return;
                        } else {
                            $reponse = "❌ Aucun covoiturage trouvé.";
                        }
                    }
                }

                echo json_encode(['reponse' => $reponse]);
            } catch (PDOException $e) {
                echo json_encode(['reponse' => "❌ Erreur base : " . $e->getMessage()]);
            }
        }
    }
}
