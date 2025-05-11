<?php

class Participant
{
    private $id_e;
    private $id;

    public function __construct($id_e, $id)
    {
        $this->id_e = $id_e;
        $this->id = $id;
    }

    // Getters
    public function getIdEvenement()
    {
        return $this->id_e;
    }

    public function getIdUtilisateur()
    {
        return $this->id;
    }

    // Setters
    public function setIdEvenement($id_e)
    {
        $this->id_e = $id_e;
    }

    public function setIdUtilisateur($id)
    {
        $this->id = $id;
    }
}
