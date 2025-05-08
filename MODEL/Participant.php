<?php

class Participant
{
    private $id_e;
    private $id_u;

    public function __construct($id_e, $id_u)
    {
        $this->id_e = $id_e;
        $this->id_u = $id_u;
    }

    // Getters
    public function getIdEvenement()
    {
        return $this->id_e;
    }

    public function getIdUtilisateur()
    {
        return $this->id_u;
    }

    // Setters
    public function setIdEvenement($id_e)
    {
        $this->id_e = $id_e;
    }

    public function setIdUtilisateur($id_u)
    {
        $this->id_u = $id_u;
    }
}
