<?php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . "/../config/constants.php";

?>
<!-----EDIT POST----->
<section class="form_section">
    <div class="container form_section-container">
        <h2>Edit Post</h2>

        <form action="<?= ROOT_URL ?>admin/edit_post_logic.php" enctype="multipart/form-data" method="POST">
        <input type="hidden" name="id" value="<?= $post['id'] ?>">
        <input type="hidden" name="previous_thumbnail_name" value="<?= $post['thumbnail'] ?>">
            <input type="text" name="title" value="<?= $post['title'] ?>" placeholder="Title">
            <textarea rows="10" name="body" placeholder="Body"><?= $post['body'] ?></textarea>
            <div class="form_control inline">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" checked>
                <label for="is_featured">Featured</label>
            </div>
            <div class="form_control">
                <label for="thumbnail">Change Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail">
            </div>
            <button type="submit" name="submit" class="btn">Update Post</button>

        </form>
    </div>
</section>
<!-----EDIT POST ENDS---->

<?php
include __DIR__ . '/../includes/footer.php';

?>