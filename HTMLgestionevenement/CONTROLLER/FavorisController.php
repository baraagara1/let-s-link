<?php
require_once __DIR__ . '/../model/FavorisModel.php';

class FavorisController {
    private $model;

    public function __construct() {
        $this->model = new FavorisModel();
    }

    public function ajouter($id, $id_e) {
        $this->model->addFavori($id, $id_e);
    }

    public function supprimer($id, $id_e) {
        $this->model->removeFavori($id, $id_e);
    }

    public function verifier($id, $id_e) {
        return $this->model->isFavori($id, $id_e);
    }

    public function listerFavoris($id) {
        return $this->model->getFavorisByUser($id);
    }
}
