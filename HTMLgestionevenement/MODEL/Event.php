<?php
class Event {
    private $nom_e;
    private $id_a;
    private $date_e;
    private $lieu_e;
    private $image;
    private $id;  // Ajout de l'ID utilisateur

    // Ajoute un constructeur qui prend en compte `id`
    public function __construct($nom_e, $id_a, $date_e, $lieu_e, $image, $id) {
        $this->nom_e = $nom_e;
        $this->id_a = $id_a;
        $this->date_e = $date_e;
        $this->lieu_e = $lieu_e;
        $this->image = $image;
        $this->id = $id;  // On affecte l'ID utilisateur
    }

    // Getter pour id
    public function getIdUtilisateur() {
        return $this->id;
    }

    // Les autres getters pour nom_e, id_a, etc.
    public function getNom() {
        return $this->nom_e;
    }

    public function getIdActivite() {
        return $this->id_a;
    }

    public function getDate() {
        return $this->date_e;
    }

    public function getLieu() {
        return $this->lieu_e;
    }

    public function getImage() {
        return $this->image;
    }
}


?>
