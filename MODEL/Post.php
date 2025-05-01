<?php
class Post {
    private ?int $id_p;
    private string $titre;
    private string $text;
    private string $jointure;

    public function __construct(
        string $titre,
        string $text,
        string $jointure,
        ?int $id_p = null
    ) {
        $this->id_p = $id_p;
        $this->titre = $titre;
        $this->text = $text;
        $this->jointure = $jointure;
    }

    // Getters
    public function getId(): ?int { return $this->id_p; }
    public function getTitre(): string { return $this->titre; }
    public function getText(): string { return $this->text; }
    public function getJointure(): string { return $this->jointure; }

    // Setters
    public function setTitre(string $titre): self { 
        $this->titre = $titre; 
        return $this; 
    }
    
    public function setText(string $text): self { 
        $this->text = $text; 
        return $this; 
    }
    
    public function setJointure(string $jointure): self { 
        $this->jointure = $jointure; 
        return $this; 
    }
}
?>
