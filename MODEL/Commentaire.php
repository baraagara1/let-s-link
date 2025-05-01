<?php
class Commentaire {
    private ?int $id_c;
    private string $contenu;
    private ?int $id_p;  // ID du post associé
    private ?int $id_u;  // ID de l'utilisateur qui a posté le commentaire
    private string $date_c;  // Date de publication

    public function __construct(
        string $contenu,
        ?int $id_p = null,
        ?int $id_u = null,
        ?int $id_c = null
    ) {
        $this->id_c = $id_c;
        $this->contenu = $contenu;
        $this->id_p = $id_p;
        $this->id_u = $id_u;
        $this->date_c = date("Y-m-d H:i:s");  // Date et heure actuelle
    }

    // Getters
    public function getId(): ?int { return $this->id_c; }
    public function getContenu(): string { return $this->contenu; }
    public function getPostId(): ?int { return $this->id_p; }
    public function getUserId(): ?int { return $this->id_u; }
    public function getDate(): string { return $this->date_c; }

    // Setters
    public function setContenu(string $contenu): self { 
        $this->contenu = $contenu; 
        return $this; 
    }

    public function setPostId(?int $id_p): self { 
        $this->id_p = $id_p; 
        return $this; 
    }

    public function setUserId(?int $id_u): self { 
        $this->id_u = $id_u; 
        return $this; 
    }
}
?>
