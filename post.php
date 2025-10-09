<?php
require_once __DIR__ . "/config/constants.php";
require_once __DIR__ . "/config/database.php";
include 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
$error = '';

if ($id > 0) {
    try {
        $stmt = $conn->prepare(
            "SELECT p.id, p.user_id, p.title, p.content, p.thumbnail, p.created_at, p.updated_at,
                    u.firstname, u.lastname, u.avatar
             FROM posts p
             JOIN users u ON u.id = p.user_id
             WHERE p.id = :id
             LIMIT 1"
        );
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$post) {
            $error = 'Post not found.';
        }
    } catch (Exception $e) {
        $error = 'Failed to load post.';
    }
} else {
    $error = 'Invalid post id.';
}
?>

<section class="posts">
    <div class="container posts_container" style="margin-top: 150px;">
        <?php if ($error) : ?>
            <div class="alert_message error"><?= htmlspecialchars($error) ?></div>
        <?php else : ?>
            <article class="post" style="width:100%">
                <div class="post_thumbnail" style="max-height:none">
                    <?php $thumb = !empty($post['thumbnail']) ? (ROOT_URL . 'assets/images/thumbnail/' . $post['thumbnail']) : (ROOT_URL . 'assets/images/thumbnail/default.jpg'); ?>
                    <a href="<?= htmlspecialchars($thumb) ?>" target="_blank" rel="noopener">
                        <img src="<?= htmlspecialchars($thumb) ?>" alt="thumbnail">
                    </a>
                </div>
                <div class="post_info">
                    <h2 class="post_title" style="margin-top:1rem;">
                        <?= htmlspecialchars($post['title']) ?>
                    </h2>
                    <div class="post_author" style="margin: .75rem 0 1rem;">
                        <div class="post_author-avatar">
                            <?php $avatar = !empty($post['avatar']) ? (ROOT_URL . 'assets/images/' . $post['avatar']) : (ROOT_URL . 'assets/images/default.png'); ?>
                            <img src="<?= htmlspecialchars($avatar) ?>" alt="author">
                        </div>
                        <div class="post_author-info">
                            <h5>By: <?= htmlspecialchars(trim(($post['firstname'] ?? '') . ' ' . ($post['lastname'] ?? ''))) ?></h5>
                            <small>
                                Created: <?= htmlspecialchars(date('M d, Y H:i', strtotime($post['created_at']))) ?>
                                <?php if (!empty($post['updated_at'])) : ?>
                                    • Updated: <?= htmlspecialchars(date('M d, Y H:i', strtotime($post['updated_at']))) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                    <div class="post_body" style="white-space:pre-wrap;">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>
                </div>
            </article>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
