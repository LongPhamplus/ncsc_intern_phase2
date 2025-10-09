<?php
require_once __DIR__ . "/config/constants.php";
require_once __DIR__ . "/config/database.php";
include 'includes/header.php';

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
try {
    if ($searchTerm !== '') {
        $stmt = $conn->prepare(
            "SELECT p.id, p.user_id, p.title, p.content, p.thumbnail, p.created_at, p.updated_at,
                     u.firstname, u.lastname
             FROM posts p
             JOIN users u ON u.id = p.user_id
             WHERE p.title LIKE :term
             ORDER BY p.created_at DESC
             LIMIT 10"
        );
        $like = "%" . $searchTerm . "%";
        $stmt->bindParam(':term', $like, PDO::PARAM_STR);
    } else {
        $stmt = $conn->prepare(
            "SELECT p.id, p.user_id, p.title, p.content, p.thumbnail, p.created_at, p.updated_at,
                     u.firstname, u.lastname
             FROM posts p
             JOIN users u ON u.id = p.user_id
             ORDER BY p.created_at DESC
             LIMIT 10"
        );
    }
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $posts = [];
}

?>

<?php
if (!function_exists('excerpt_text')) {
    function excerpt_text($text, $limit = 140, $suffix = '...') {
        $text = (string)($text ?? '');
        if (function_exists('mb_strimwidth')) {
            return mb_strimwidth($text, 0, $limit, $suffix, 'UTF-8');
        }
        if (function_exists('mb_substr') && function_exists('mb_strlen')) {
            if (mb_strlen($text, 'UTF-8') <= $limit) return $text;
            $cut = max(0, $limit - strlen($suffix));
            return mb_substr($text, 0, $cut, 'UTF-8') . $suffix;
        }
        if (strlen($text) <= $limit) return $text;
        $cut = max(0, $limit - strlen($suffix));
        return substr($text, 0, $cut) . $suffix;
    }
}
?>

<section class="search_bar">
    <form class="container search_bar-container" action="<?= ROOT_URL ?>index.php" method="GET">
        <div>
            <i class="uil uil-search"></i>
            <input type="search" name="search" placeholder="Search by title" value="<?= htmlspecialchars($searchTerm) ?>">
        </div>
        <button type="submit" name="submit" class="btn">GO</button>
    </form>

</section>
<section class="posts " >
    <div class="container posts_container">
        <?php if (!empty($posts)) : ?>
            <?php foreach ($posts as $post) : ?>
                <article class="post">
                    <div class="post_thumbnail">
                        <?php $thumb = !empty($post['thumbnail']) ? (ROOT_URL . 'assets/images/thumbnail/' . $post['thumbnail']) : (ROOT_URL . 'assets/images/thumbnail/default.jpg'); ?>
                        <a href="<?= htmlspecialchars($thumb) ?>" target="_blank" rel="noopener">
                            <img src="<?= htmlspecialchars($thumb) ?>" alt="thumbnail">
                        </a>
                    </div>
                    <div class="post_info">
                        <h3 class="post_title">
                            <a href="<?= ROOT_URL ?>post.php?id=<?= (int)$post['id'] ?>">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </h3>
                        <p class="post_body">
                            <?= htmlspecialchars(excerpt_text($post['content'], 140)) ?>
                        </p>
                        <div class="post_meta">
                            <small>ID: <?= htmlspecialchars($post['id']) ?> | By: <?= htmlspecialchars(trim(($post['firstname'] ?? '') . ' ' . ($post['lastname'] ?? ''))) ?></small><br>
                            <small>Created: <?= htmlspecialchars(date('M d, Y H:i', strtotime($post['created_at']))) ?></small><br>
                            <small>Updated: <?= htmlspecialchars(!empty($post['updated_at']) ? date('M d, Y H:i', strtotime($post['updated_at'])) : '-') ?></small>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="alert_message error">No posts yet.</div>
        <?php endif; ?>
    </div>
</section>


<?php
include 'includes/footer.php';

?>