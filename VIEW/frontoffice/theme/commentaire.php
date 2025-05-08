<?php
require_once '../../../controller/PostController.php';
require_once '../../../controller/CommentaireController.php';
include 'chatbot.php'; 

function detectGrosMotsAvecOpenAI($texte) {
    //api
    
    $prompt = "Le texte suivant contient-il des propos offensants, haineux ou inappropriés ?en fr arabe(tunisien) et englais  lit tout la chaine espace par espace et cherche chaque mots 
Réponds uniquement par \"oui\" ou \"non\". 
Texte : \"$texte\"";

    $data = [
        'model' => 'gpt-4o',
        'messages' => [
            ['role' => 'system', 'content' => 'Tu es un modérateur intelligent de commentaires.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return false;

    $result = json_decode($response, true);
    $content = strtolower(trim($result['choices'][0]['message']['content'] ?? ''));

    return (strpos($content, 'oui') !== false); 
}



// Récupérer tous les posts depuis la base de données
$controller = new PostController();
$posts = $controller->getAllPosts();

$commentaireController = new CommentaireController();

// Traitement ajout de commentaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contenu'], $_POST['id_post'])) {
    $contenu = $_POST['contenu'];
    $id_p = $_POST['id_post'];
    $id_u = 1; // à remplacer par $_SESSION['id_u'] si connexion

    if (!empty(trim($contenu))) {
        if (detectGrosMotsAvecOpenAI($contenu)) {
            $erreurCommentaire = "⚠️ Ce commentaire contient des propos inappropriés.";
        } else {
            $commentaireController->addCommentaire($contenu, $id_p, $id_u);
            header("Location: commentaire.php");
            exit;
        }
    }
}
//MODIFICATION COM

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_c']) && isset($_POST['contenu'])) {
        $id_c = $_POST['id_c'];
        $contenu = $_POST['contenu'];

        $controller = new CommentaireController();
        $controller->updateCommentaire($contenu, $id_c);
        header("Location: commentaire.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Let's Link - Blog Communautaire</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #06BBCC;
            --secondary-color: #f8f9fa;
            --accent-color: #ff6f61;
            --dark-color: #343a40;
            --light-color: #ffffff;
            --facebook-blue: #1877f2;
            --facebook-hover-blue: #166fe5;
            --facebook-light-gray: #f0f2f5;
            --facebook-gray: #e4e6eb;
            --facebook-dark-gray: #65676b;
        }

        body {
            background-color: var(--facebook-light-gray);
            font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Posts Section - Facebook Style */
        .post-card {
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            margin-bottom: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: var(--light-color);
            animation: fadeInUp 0.6s ease forwards;
            border: 1px solid #dddfe2;
        }

        .post-header {
            padding: 12px 16px;
            display: flex;
            align-items: center;
        }

        .post-user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .post-user-info {
            flex-grow: 1;
        }

        .post-user-name {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 2px;
            font-size: 15px;
        }

        .post-date {
            font-size: 13px;
            color: var(--facebook-dark-gray);
        }

        .post-options {
            position: relative;
        }

        .post-options-btn {
            background: none;
            border: none;
            color: var(--facebook-dark-gray);
            font-size: 20px;
            cursor: pointer;
            padding: 0 8px;
        }

        .post-options-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
            z-index: 10;
            min-width: 200px;
            display: none;
        }

        .post-options-menu.show {
            display: block;
        }

        .post-options-item {
            padding: 8px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .post-options-item:hover {
            background-color: var(--facebook-light-gray);
        }

        .post-options-item i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }

        .post-content {
            padding: 0 16px 12px;
            color: #050505;
            line-height: 1.4;
            font-size: 15px;
        }

        .post-media {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            background-color: #f0f2f5;
        }

        .post-footer {
            padding: 6px 16px 8px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .post-stats {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dddfe2;
            color: var(--facebook-dark-gray);
            font-size: 15px;
        }

        .post-actions {
            display: flex;
            justify-content: space-around;
            padding: 4px 0;
        }

        .post-action-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 0;
            border-radius: 4px;
            background: none;
            border: none;
            color: var(--facebook-dark-gray);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .post-action-btn:hover {
            background-color: var(--facebook-light-gray);
        }

        .post-action-btn i {
            margin-right: 8px;
            font-size: 18px;
        }

        .post-action-btn.liked {
            color: var(--facebook-blue);
        }

        /* Comments Section - Facebook Style */
        .comments-section {
            padding: 8px 16px;
            border-top: 1px solid #dddfe2;
            background-color: #f0f2f5;
        }

        .comment-card {
            display: flex;
            padding: 8px 0;
        }

        .comment-user-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .comment-content {
            background-color: white;
            border-radius: 18px;
            padding: 8px 12px;
            flex-grow: 1;
            position: relative;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 2px;
        }

        .comment-user-name {
            font-weight: 600;
            font-size: 13px;
            color: #050505;
            margin-right: 4px;
        }

        .comment-text {
            font-size: 15px;
            color: #050505;
            line-height: 1.4;
            word-break: break-word;
        }

        .comment-actions {
            display: flex;
            margin-top: 4px;
            font-size: 12px;
            color: var(--facebook-dark-gray);
        }

        .comment-action {
            margin-right: 12px;
            cursor: pointer;
            font-weight: 600;
        }

        .comment-action:hover {
            text-decoration: underline;
        }

        .comment-edit-form {
            display: none;
            margin-top: 8px;
        }

        .comment-edit-form textarea {
            width: 100%;
            border-radius: 18px;
            border: 1px solid #dddfe2;
            padding: 8px 12px;
            resize: none;
            font-family: inherit;
            font-size: 15px;
        }

        .comment-edit-buttons {
            display: flex;
            margin-top: 4px;
        }

        .comment-edit-btn {
            padding: 4px 8px;
            margin-right: 8px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }

        .comment-save-btn {
            background-color: var(--facebook-blue);
            color: white;
            border: none;
        }

        .comment-cancel-btn {
            background: none;
            border: none;
            color: var(--facebook-dark-gray);
        }

        /* Comment Form - Facebook Style */
        .comment-form {
            display: flex;
            padding: 8px 16px;
            border-top: 1px solid #dddfe2;
            align-items: center;
        }

        .comment-form-img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .comment-form-input {
            flex-grow: 1;
            position: relative;
        }

        .comment-form-input textarea {
            width: 100%;
            border-radius: 18px;
            border: none;
            background-color: #f0f2f5;
            padding: 8px 12px;
            resize: none;
            font-family: inherit;
            font-size: 15px;
            min-height: 36px;
            max-height: 100px;
        }

        .comment-form-input textarea:focus {
            outline: none;
            background-color: #e4e6eb;
        }

        .comment-form-submit {
            margin-left: 8px;
            background-color: var(--facebook-blue);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-weight: 600;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .comment-form-submit.active {
            opacity: 1;
        }

        /* Floating Add Button */
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--facebook-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s;
            text-decoration: none;
        }

        .floating-btn:hover {
            background-color: var(--facebook-hover-blue);
            transform: scale(1.1);
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .post-card {
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid bg-dark text-light px-0 py-2">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <span class="fa fa-phone-alt me-2"></span>
                    <span>+216 97640509</span>
                </div>
                <div class="h-100 d-inline-flex align-items-center">
                    <span class="far fa-envelope me-2"></span>
                    <span>letslink@gmail.com</span>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center mx-n2">
                    <span>Follow Us:</span>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
        <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img src="logo.png" alt="logo" style="width:140px; height:auto;">
        </a>
        
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.html" class="nav-item nav-link">Home</a>
                <a href="about.html" class="nav-item nav-link">About</a>
                <a href="service.html" class="nav-item nav-link">Services</a>
                <a href="project.html" class="nav-item nav-link active">Blog</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu bg-light m-0">
                        <a href="commentaire.html" class="dropdown-item">Commentaires</a>
                    </div>
                </div>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
            </div>
            <a href="contact.php" class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block">Create Post <i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Page Header Start (Section commune) -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white mb-4 animated slideInDown">Blog Communautaire</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Blog</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php foreach ($posts as $post): ?>
                    <div class="post-card mb-4">
                        <!-- Post Header -->
                        <div class="post-header">
                            
                            <div class="post-user-info">
                                <div class="post-user-name">Utilisateur</div>

                            </div>
                            <div class="post-options">
                                <button class="post-options-btn" onclick="toggleOptionsMenu(this)">⋯</button>
                                <div class="post-options-menu">
                                    <div class="post-options-item" onclick="editPost(<?= $post['id_p'] ?>)">
                                        <i class="fas fa-edit"></i> Modifier
                                    </div>
                                    <div class="post-options-item text-danger" onclick="deletePost(<?= $post['id_p'] ?>)">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Post Content -->
                        <div class="post-content">
                            <p><?= nl2br(htmlspecialchars($post['text'])) ?></p>
                        </div>
                        
                        <!-- Post Media -->
                        <?php if ($post['jointure']): ?>
                            <div class="post-media-container">
                                <?php
                                $filePath = '../../uploads/' . $post['jointure'];
                                $fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
                                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                    echo '<img src="' . $filePath . '" class="post-media" alt="Post Image">';
                                } elseif (in_array($fileExt, ['mp4', 'avi', 'mov', 'mkv'])) {
                                    echo '<video controls class="post-media"><source src="' . $filePath . '" type="video/' . $fileExt . '"></video>';
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Post Stats -->
                        <div class="post-stats">
                            <span><i class="fas fa-thumbs-up"></i> 42</span>
                            <span>12 commentaires</span>
                        </div>
                        
                        <!-- Post Actions -->
                        <div class="post-actions">
                            <button class="post-action-btn">
                                <i class="far fa-thumbs-up"></i> J'aime
                            </button>
                            <button class="post-action-btn">
                                <i class="far fa-comment"></i> Commenter
                            </button>
                            <button class="post-action-btn">
                                <i class="fas fa-share"></i> Partager
                            </button>
                        </div>
                        
                        <div class="comments-section">
    <?php
    $comments = $commentaireController->getCommentairesByPostId($post['id_p']);
    if (!empty($comments)): 
        $first = array_shift($comments); // extrait le premier
    ?>
        <!-- Premier commentaire (toujours affiché) -->
        <div class="comment-card" id="comment-<?= $first['id_c'] ?>">
            <div class="comment-content">
                <div class="comment-header">
                    <span class="comment-user-name">Utilisateur</span>
                    <span class="comment-date"><?= date('d M Y', strtotime($first['date_c'])) ?></span>
                </div>
                <div class="comment-text" id="comment-text-<?= $first['id_c'] ?>">
                    <?= nl2br(htmlspecialchars($first['contenu'])) ?>
                </div>
                <form method="POST" action="commentaire.php" class="comment-edit-form" id="edit-form-<?= $first['id_c'] ?>">
                    <input type="hidden" name="id_c" value="<?= $first['id_c'] ?>">
                    <textarea name="contenu"><?= htmlspecialchars($first['contenu']) ?></textarea>
                    <div class="comment-edit-buttons">
                        <button type="submit" class="comment-edit-btn comment-save-btn">Enregistrer</button>
                        <button type="button" class="comment-edit-btn comment-cancel-btn" onclick="cancelEdit(<?= $first['id_c'] ?>)">Annuler</button>
                    </div>
                </form>
                <div class="comment-actions">
                    <span class="comment-action" onclick="editComment(<?= $first['id_c'] ?>)">Modifier</span>
                    <span class="comment-action" onclick="deleteComment(<?= $first['id_c'] ?>)">Supprimer</span>
                </div>
            </div>
        </div>

        <?php if (!empty($comments)): ?>
            <!-- Bouton Afficher plus -->
            <div class="text-center">
                <button class="btn btn-sm btn-link text-primary" id="toggleBtn-<?= $post['id_p'] ?>" onclick="toggleComments(<?= $post['id_p'] ?>)">Afficher plus</button>
            </div>

            <!-- Commentaires restants -->
            <div id="more-comments-<?= $post['id_p'] ?>" style="display: none;">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-card" id="comment-<?= $comment['id_c'] ?>">
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-user-name">Utilisateur</span>
                                <span class="comment-date"><?= date('d M Y', strtotime($comment['date_c'])) ?></span>
                            </div>
                            <div class="comment-text" id="comment-text-<?= $comment['id_c'] ?>">
                                <?= nl2br(htmlspecialchars($comment['contenu'])) ?>
                            </div>
                            <form method="POST" action="commentaire.php" class="comment-edit-form" id="edit-form-<?= $comment['id_c'] ?>">
                                <input type="hidden" name="id_c" value="<?= $comment['id_c'] ?>">
                                <textarea name="contenu"><?= htmlspecialchars($comment['contenu']) ?></textarea>
                                <div class="comment-edit-buttons">
                                    <button type="submit" class="comment-edit-btn comment-save-btn">Enregistrer</button>
                                    <button type="button" class="comment-edit-btn comment-cancel-btn" onclick="cancelEdit(<?= $comment['id_c'] ?>)">Annuler</button>
                                </div>
                            </form>
                            <div class="comment-actions">
                                <span class="comment-action" onclick="editComment(<?= $comment['id_c'] ?>)">Modifier</span>
                                <span class="comment-action" onclick="deleteComment(<?= $comment['id_c'] ?>)">Supprimer</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-2 text-muted">Aucun commentaire</div>
    <?php endif; ?>

    <!-- Formulaire de commentaire -->
    <form method="POST" action="commentaire.php" class="comment-form">
    <input type="hidden" name="id_post" value="<?= $post['id_p'] ?>">
    <div class="comment-form-input">
        <textarea name="contenu" placeholder="Écrire un commentaire..." oninput="adjustTextarea(this)"></textarea>
    </div>
    <button type="submit" class="comment-form-submit" disabled>Publier</button>

    <?php if (isset($erreurCommentaire) && isset($_POST['id_post']) && $_POST['id_post'] == $post['id_p']): ?>
        <div class="alert alert-danger mt-2"><?= $erreurCommentaire ?></div>
    <?php endif; ?>
</form>

    
</div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Floating Add Button -->
    <a href="ajouter_post.php" class="floating-btn" title="Créer un nouveau post">
        <i class="fas fa-plus"></i>
    </a>

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer mt-5 py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Our Office</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Tunis, Tunisia</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+216 97640509</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>letslink@gmail.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Services</h4>
                    <a class="btn btn-link" href="">Blog Communautaire</a>
                    <a class="btn btn-link" href="">Événements</a>
                    <a class="btn btn-link" href="">Réseautage</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Quick Links</h4>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                    <a class="btn btn-link" href="">Our Services</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Newsletter</h4>
                    <p>Abonnez-vous pour ne rien manquer de notre actualité</p>
                    <div class="position-relative w-100">
                        <input class="form-control bg-light border-light w-100 py-3 ps-4 pe-5" type="text" placeholder="Votre email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">S'abonner</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a class="border-bottom" href="#">Let's Link</a>, All Right Reserved.
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!-- Custom Script -->
    <script>
        // Toggle post options menu
        function toggleOptionsMenu(button) {
            const menu = button.nextElementSibling;
            menu.classList.toggle('show');
            
            // Close when clicking outside
            document.addEventListener('click', function closeMenu(e) {
                if (!menu.contains(e.target) && e.target !== button) {
                    menu.classList.remove('show');
                    document.removeEventListener('click', closeMenu);
                }
            });
        }
        
        // Edit post function
        function editPost(postId) {
            window.location.href = 'modifier_post.php?id=' + postId;
        }
        
        // Delete post function
        function deletePost(postId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce post ?')) {
                window.location.href = 'supprimer_post.php?id=' + postId;
            }
        }
        
        // Edit comment function
        function editComment(commentId) {
            document.getElementById('comment-text-' + commentId).style.display = 'none';
            document.getElementById('edit-form-' + commentId).style.display = 'block';
        }
        
        // Cancel edit comment
        function cancelEdit(commentId) {
            document.getElementById('comment-text-' + commentId).style.display = 'block';
            document.getElementById('edit-form-' + commentId).style.display = 'none';
        }
        
        // Delete comment function
        function deleteComment(commentId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
                window.location.href = 'supprimer_commentaire.php?id=' + commentId;
            }
        }
        
        // Adjust textarea height dynamically
        function adjustTextarea(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
            
            // Enable/disable submit button based on content
            const submitBtn = textarea.closest('.comment-form').querySelector('.comment-form-submit');
            submitBtn.disabled = textarea.value.trim() === '';
            submitBtn.classList.toggle('active', !submitBtn.disabled);
        }
        
        // Like button functionality
        document.querySelectorAll('.post-action-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (this.innerHTML.includes('far')) { // Not liked
                    this.innerHTML = this.innerHTML.replace('far', 'fas');
                    if (this.innerHTML.includes('J\'aime')) {
                        this.innerHTML = this.innerHTML.replace('J\'aime', 'Aimé');
                    }
                    this.style.color = 'var(--facebook-blue)';
                } else { // Already liked
                    this.innerHTML = this.innerHTML.replace('fas', 'far');
                    if (this.innerHTML.includes('Aimé')) {
                        this.innerHTML = this.innerHTML.replace('Aimé', 'J\'aime');
                    }
                    this.style.color = 'var(--facebook-dark-gray)';
                }
            });
        });
        
        // Initialize textareas
        document.querySelectorAll('.comment-form textarea').forEach(textarea => {
            adjustTextarea(textarea);
        });
    </script>
    <script>
function toggleComments(postId) {
    const section = document.getElementById('more-comments-' + postId);
    const button = document.getElementById('toggleBtn-' + postId);

    if (section.style.display === 'none') {
        section.style.display = 'block';
        button.innerText = 'Afficher moins';
    } else {
        section.style.display = 'none';
        button.innerText = 'Afficher plus';
    }
}
</script>

</body>
</html>