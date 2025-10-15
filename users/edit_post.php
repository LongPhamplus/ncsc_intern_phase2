<?php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . '/../config/middleware.php';
require_once __DIR__ . '/../config/database.php';

checkLogin();

$currentUserId = $_SESSION['user-id'] ?? null;
$postId = $_GET['id'] ?? null;
if (!$postId || !$currentUserId) {
    header("Location: " . ROOT_URL . "users/dashboard.php");
    exit;
}

$userRole = $_SESSION['role'];

if ($userRole === 'admin') {
    $sql = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$postId]);
} else {
    $sql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$postId, $currentUserId]);
}
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($post)) {
    header("Location: " . ROOT_URL . "users/manage_posts.php");
    exit;
}   

?>
<section class="form_section">
    <div class="container form_section-container">
        <?php if (isset($_SESSION['edit-post-error'])) :?>
        <div class="alert_message error container">
            <p>
                <?= $_SESSION['edit-post-error'];
                unset($_SESSION['edit-post-error']);
                ?>
            </p>
        </div>
        <?php endif ?>
        <h2>Edit Post</h2>

        <form action="<?= ROOT_URL ?>users/edit_post_logic.php" enctype="multipart/form-data" method="POST">
        <input type="hidden" name="id" value="<?= $post['id'] ?>">
        <input type="hidden" name="previous_thumbnail_name" value="<?= $post['thumbnail'] ?>">
            <input type="text" name="title" value="<?= $post['title'] ?>" placeholder="Title">
            <textarea rows="10" name="body" placeholder="Body"><?= $post['content'] ?></textarea>
            <div class="form_control">
                <label for="thumbnail">Change Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail">
            </div>
            <button type="submit" name="submit" class="btn">Update Post</button>

        </form>
    </div>
</section>

<?php
include __DIR__ . '/../includes/footer.php';

?>