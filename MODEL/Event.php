<?php
class Event {
    private ?int $id_e;
    private string $nom_e;
    private int $id_a; // au lieu de type

    private string $date_e;
    private string $lieu_e;
    private string $image;

    public function __construct(
        string $nom_e,
        int $id_a,  // int au lieu de string
        string $date_e,
        string $lieu_e,
        string $image,
        ?int $id_e = null
    ) {
        $this->id_e = $id_e;
        $this->nom_e = $nom_e;
        $this->id_a = $id_a;
        $this->date_e = $date_e;
        $this->lieu_e = $lieu_e;
        $this->image = $image;
    }

    // Getters
    public function getId(): ?int { return $this->id_e; }
    public function getNom(): string { return $this->nom_e; }
    public function getIdActivite(): int { return $this->id_a; }
    public function getDate(): string { return $this->date_e; }
    public function getLieu(): string { return $this->lieu_e; }
    public function getImage(): string { return $this->image; }

    // Setters
    public function setNom(string $nom_e): self { $this->nom_e = $nom_e; return $this; }
    public function setIdActivite(int $id_a): self { $this->id_a = $id_a; return $this; }
    public function setDate(string $date_e): self { $this->date_e = $date_e; return $this; }
    public function setLieu(string $lieu_e): self { $this->lieu_e = $lieu_e; return $this; }
    public function setImage(string $image): self { $this->image = $image; return $this; }
}

?>
