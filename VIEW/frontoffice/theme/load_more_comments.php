<?php
require_once '../../../controller/CommentaireController.php';
$commentaireController = new CommentaireController();

header('Content-Type: text/html');

$postId = (int)$_GET['post_id'];
$offset = (int)$_GET['offset'];
$limit = 3; // Nombre de commentaires à charger

$comments = $commentaireController->getPaginatedCommentaires($postId, $offset, $limit);

foreach ($comments as $comment) {
    // Utilisez le même template que pour l'affichage initial
    echo '<div class="comment-card">';
    echo '<div class="comment-body">';
    echo '<div id="comment-content-'.$comment['id_c'].'">';
    echo '<p>'.nl2br(htmlspecialchars($comment['contenu'])).'</p>';
    echo '<small class="text-muted">Posté le '.date('d/m/Y H:i', strtotime($comment['date_c'])).'</small>';
    echo '</div>';
    
    // Formulaire d'édition
    echo '<form method="POST" action="commentaire.php" class="edit-form" id="edit-form-'.$comment['id_c'].'" style="display:none;">';
    echo '<input type="hidden" name="id_c" value="'.$comment['id_c'].'">';
    echo '<textarea name="contenu" class="form-control mb-2">'.htmlspecialchars($comment['contenu']).'</textarea>';
    echo '<button type="submit" class="btn btn-sm btn-success">Enregistrer</button>';
    echo '<button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit('.$comment['id_c'].')">Annuler</button>';
    echo '</form>';
    
    // Boutons d'action
    echo '<div class="comment-actions mt-2">';
    echo '<button class="btn btn-sm btn-outline-primary" onclick="toggleEdit('.$comment['id_c'].')">';
    echo '<i class="fas fa-edit"></i> Modifier';
    echo '</button>';
    echo '<a href="supprimer_commentaire.php?id='.$comment['id_c'].'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce commentaire ?\')">';
    echo '<i class="fas fa-trash"></i> Supprimer';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>