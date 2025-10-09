<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/middleware.php';

checkLogin();

// Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['delete-user'] = 'You are not authorized to delete users.';
    header('Location: ' . ROOT_URL . 'users/manage_users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . 'users/manage_users.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    $_SESSION['delete-user'] = 'Invalid user id.';
    header('Location: ' . ROOT_URL . 'users/manage_users.php');
    exit;
}

// Prevent deleting yourself (optional safety)
if (isset($_SESSION['user-id']) && (int)$_SESSION['user-id'] === $id) {
    $_SESSION['delete-user'] = 'You cannot delete your own account.';
    header('Location: ' . ROOT_URL . 'users/manage_users.php');
    exit;
}

try {
    // Fetch user info (to get avatar) and ensure exists
    $stmt = $conn->prepare('SELECT id, avatar FROM users WHERE id = :id LIMIT 1');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['delete-user'] = 'User not found.';
        header('Location: ' . ROOT_URL . 'users/manage_users.php');
        exit;
    }

    // Remove user avatar if not default
    if (!empty($user['avatar']) && strtolower($user['avatar']) !== 'default.png') {
        $avatarPath = __DIR__ . '/../assets/images/' . $user['avatar'];
        if (is_file($avatarPath)) {
            @unlink($avatarPath);
        }
    }

    // Remove user's posts and thumbnails first (to avoid orphan files)
    $ps = $conn->prepare('SELECT id, thumbnail FROM posts WHERE user_id = :uid');
    $ps->bindParam(':uid', $id, PDO::PARAM_INT);
    $ps->execute();
    $posts = $ps->fetchAll(PDO::FETCH_ASSOC);

    foreach ($posts as $post) {
        if (!empty($post['thumbnail']) && strtolower($post['thumbnail']) !== 'default.jpg') {
            $thumbPath = __DIR__ . '/../assets/images/thumbnail/' . $post['thumbnail'];
            if (is_file($thumbPath)) {
                @unlink($thumbPath);
            }
        }
    }

    // Delete posts
    $delPosts = $conn->prepare('DELETE FROM posts WHERE user_id = :uid');
    $delPosts->bindParam(':uid', $id, PDO::PARAM_INT);
    $delPosts->execute();

    // Finally delete user
    $delUser = $conn->prepare('DELETE FROM users WHERE id = :id');
    $delUser->bindParam(':id', $id, PDO::PARAM_INT);
    $delUser->execute();

    $_SESSION['delete-user-success'] = 'User deleted successfully.';
} catch (Exception $e) {
    $_SESSION['delete-user'] = 'Failed to delete user.';
}

header('Location: ' . ROOT_URL . 'users/manage_users.php');
exit;
