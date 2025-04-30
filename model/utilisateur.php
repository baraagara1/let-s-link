<?php
class Utilisateur {
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private int $telephone;
    private string $adresse;
    private string $motpasse;
    private string $role;

    public function __construct(
        string $nom,
        string $prenom,
        string $email,
        int $telephone,
        string $adresse,
        string $motpasse,
        string $role,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->adresse = $adresse;
        $this->motpasse = $motpasse;
        $this->role = $role;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getTelephone(): int { return $this->telephone; }
    public function getAdresse(): string { return $this->adresse; }
    public function getMotpasse(): string { return $this->motpasse; }
    public function getRole(): string { return $this->role; }

    // Setters
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function setTelephone(int $telephone): self { $this->telephone = $telephone; return $this; }
    public function setAdresse(string $adresse): self { $this->adresse = $adresse; return $this; }
    public function setMotpasse(string $motpasse): self { $this->motpasse = $motpasse; return $this; }
    public function setRole(string $role): self { $this->role = $role; return $this; }
}
?>
