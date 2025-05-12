<?php
require_once 'config/database.php';

class Signalement {
    private $id_signalement;
    private $id_cov;
    private $message;
    private $date_signalement;

    public function __construct($id_cov, $message, $date_signalement = null, $id_signalement = null) {
        $this->id_signalement = $id_signalement;
        $this->id_cov = $id_cov;
        $this->message = $message;
        $this->date_signalement = $date_signalement ?? date('Y-m-d H:i:s');
    }

    // Getters
    public function getId() { return $this->id_signalement; }
    public function getIdCov() { return $this->id_cov; }
    public function getMessage() { return $this->message; }
    public function getDateSignalement() { return $this->date_signalement; }

    // Setters
    public function setMessage($val) { $this->message = $val; }

    // Sauvegarde
    public function save() {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO signalements (id_cov, message, date_signalement) VALUES (?, ?, ?)");
        $stmt->execute([$this->id_cov, $this->message, $this->date_signalement]);
    }

    // Récupération regroupée par covoiturage
    public static function parCovoiturage() {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM signalements ORDER BY date_signalement DESC");
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($all as $sig) {
            $result[$sig['id_cov']][] = $sig;
        }
        return $result;
    }
}
