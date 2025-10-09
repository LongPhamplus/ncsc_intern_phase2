<?php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/middleware.php';

checkLogin();

$title = $_SESSION['form_data']['title'] ?? '';
$body  = $_SESSION['form_data']['body'] ?? '';

unset($_SESSION['form_data']);
?>

<section class="form_section">
    <div class="container form_section-container">
        <h2>Add post</h2>

        <?php if (!empty($_SESSION['add-post'])) : ?>
            <div class="alert_message error">
                <p><?= htmlspecialchars($_SESSION['add-post']) ?></p>
            </div>
            <?php unset($_SESSION['add-post']); ?>
        <?php endif; ?>

        <form action="<?= htmlspecialchars(ROOT_URL . 'users/add_post_logic.php') ?>" method="POST" enctype="multipart/form-data">
            <div class="form_control">
                <label for="title">Title</label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($title) ?>"
                       placeholder="Title" required>
            </div>

            <div class="form_control">
                <label for="body">Body</label>
                <textarea id="body" name="body" rows="10" placeholder="Body" required><?= htmlspecialchars($body) ?></textarea>
            </div>

            <div class="form_control">
                <label for="thumbnail">Add Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*">
            </div>

            <button type="submit" name="submit" class="btn">Add Post</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
