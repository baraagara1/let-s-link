<?php
require_once '../../../controller/PostController.php';

// Check if the post ID is provided in the URL
if (isset($_GET['id'])) {
    $id_p = $_GET['id'];  // Get the Post ID from the URL

    // Create a PostController instance to call the delete function
    $controller = new PostController();
    if ($controller->deletePost($id_p)) {
        // Redirect to the blog management page after deletion
        header("Location: gestion_blog.php");
        exit();
    } else {
        echo "Error deleting the post.";
    }
} else {
    echo "Post ID not provided.";
}
?>
