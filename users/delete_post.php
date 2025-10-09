<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/middleware.php';

checkLogin();

// Validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['delete-post-error'] = 'Invalid post id.';
    header('Location: ' . ROOT_URL . 'users/manage_posts.php');
    exit;
}

try {
    // Fetch post to verify ownership and get thumbnail
    $stmt = $conn->prepare('SELECT id, user_id, thumbnail FROM posts WHERE id = :id LIMIT 1');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        $_SESSION['delete-post-error'] = 'Post not found.';
        header('Location: ' . ROOT_URL . 'users/manage_posts.php');
        exit;
    }

    // Authorization: owner or admin
    $isOwner = isset($_SESSION['user-id']) && ((int)$_SESSION['user-id'] === (int)$post['user_id']);
    $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    if (!$isOwner && !$isAdmin) {
        $_SESSION['delete-post-error'] = 'You are not authorized to delete this post.';
        header('Location: ' . ROOT_URL . 'users/manage_posts.php');
        exit;
    }

    // Delete the post
    $del = $conn->prepare('DELETE FROM posts WHERE id = :id');
    $del->bindParam(':id', $id, PDO::PARAM_INT);
    $del->execute();

    // Remove thumbnail file if not default
    if (!empty($post['thumbnail']) && strtolower($post['thumbnail']) !== 'default.jpg') {
        $thumbPath = __DIR__ . '/../assets/images/thumbnail/' . $post['thumbnail'];
        if (is_file($thumbPath)) {
            @unlink($thumbPath);
        }
    }

    $_SESSION['delete-post-success'] = 'Post deleted successfully.';
} catch (Exception $e) {
    $_SESSION['delete-post-error'] = 'Failed to delete the post.';
}

header('Location: ' . ROOT_URL . 'users/manage_posts.php');
exit;
?>

