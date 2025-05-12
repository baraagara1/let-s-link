<?php
require_once 'config/database.php';

class Covoiturage {
    private $id_cov;
    private $lieu_depart;
    private $destination;
    private $date;
    private $heure_depart;
    private $place_dispo;
    private $prix_c;
    private $user_id;

    public function __construct($lieu, $dest, $date, $heure, $places, $prix, $id_user, $id_cov = null) {
        $this->id_cov = $id_cov;
        $this->lieu_depart = $lieu;
        $this->destination = $dest;
        $this->date = $date;
        $this->heure_depart = $heure;
        $this->place_dispo = $places;
        $this->prix_c = $prix;
        $this->user_id = $id_user;
    }

    // Getters
    public function getIdCov() { return $this->id_cov; }
    public function getLieuDepart() { return $this->lieu_depart; }
    public function getDestination() { return $this->destination; }
    public function getDate() { return $this->date; }
    public function getHeureDepart() { return $this->heure_depart; }
    public function getPlaces() { return $this->place_dispo; }
    public function getPrix() { return $this->prix_c; }
    public function getIdUtilisateur() { return $this->user_id; }

    // Setters
    public function setLieuDepart($val) { $this->lieu_depart = $val; }
    public function setDestination($val) { $this->destination = $val; }
    public function setDate($val) { $this->date = $val; }
    public function setHeureDepart($val) { $this->heure_depart = $val; }
    public function setPlaces($val) { $this->place_dispo = $val; }
    public function setPrix($val) { $this->prix_c = $val; }
    public function setIdUtilisateur($val) { $this->user_id = $val; }

    // Méthodes de base (utilise PDO)
    public static function getAll() {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM covoiturage ORDER BY date ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save() {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO covoiturage (lieu_depart, destination, date, heure_depart, place_dispo, prix_c, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $this->lieu_depart,
            $this->destination,
            $this->date,
            $this->heure_depart,
            $this->place_dispo,
            $this->prix_c,
            $this->user_id
        ]);
    }
    

    public static function findById($id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE id_cov = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE covoiturage SET lieu_depart=?, destination=?, date=?, heure_depart=?, place_dispo=?, prix_c=?, user_id=? WHERE id_cov=?");
        $stmt->execute([
            $this->lieu_depart,
            $this->destination,
            $this->date,
            $this->heure_depart,
            $this->place_dispo,
            $this->prix_c,
            $this->user_id,
            $this->id_cov
        ]);
    }

    public static function delete($id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM covoiturage WHERE id_cov = ?");
        $stmt->execute([$id]);
    }



    public static function ajouter($data) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $stmt = $pdo->prepare("INSERT INTO covoiturage (lieu_depart, destination, date, heure_depart, place_dispo, prix_c, user_id)
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
    
            $stmt->execute([
                $data['lieu_depart'],
                $data['destination'],
                $data['date'],
                $data['heure_depart'],
                $data['place_dispo'],
                $data['prix_c'],
                $_SESSION['user_id'] ?? 0
            ]);
        } catch (PDOException $e) {
            die("❌ Erreur PDO : " . $e->getMessage());
        }
    }


    public static function supprimer($id) {
        $pdo = new PDO("mysql:host=localhost;dbname=lets_link", "root", "");
        $stmt = $pdo->prepare("DELETE FROM covoiturage WHERE id_cov = ?");
        return $stmt->execute([$id]);
    }
    
    
}
