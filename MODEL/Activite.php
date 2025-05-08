<?php
class Activite {
    private ?int $id_a;
    private string $nom_a;
    private string $description;
    private string $image;
    private int $id_u;

    public function __construct(
        string $nom_a, 
        string $description, 
        string $image, 
        int $id_u,
        ?int $id_a = null
    ) {
        $this->id_a = $id_a;
        $this->nom_a = $nom_a;
        $this->description = $description;
        $this->image = $image;
        $this->id_u = $id_u;
    }

    // Getters
    public function getId(): ?int { return $this->id_a; }
    public function getNom(): string { return $this->nom_a; }
    public function getDescription(): string { return $this->description; }
    public function getImage(): string { return $this->image; }
    public function getUserId(): int { return $this->id_u; }

    // Setters
    public function setNom(string $nom_a): self {
        $this->nom_a = $nom_a;
        return $this;
    }

    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    public function setImage(string $image): self {
        $this->image = $image;
        return $this;
    }

    public function setUserId(int $id_u): self {
        $this->id_u = $id_u;
        return $this;
    }
}
?>
