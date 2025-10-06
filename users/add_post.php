<?php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . "/../config/constants.php";

?>

<!-----ADD POST----->
<section class="form_section">
    <div class="container form_section-container">
        <h2>Add Post</h2>
         <!--/ EDIT POST ERROR MESSAGE-->
         <?php if (isset($_SESSION['add-post'])) : //shows if edit post was NOT successful 
    ?>
        <div class="alert_message error">
            <p>
                <?= $_SESSION['add-post'];
                //DELETE AFER EXECUTING
                unset($_SESSION['add-post']);
                ?>
            </p>
        </div>
    <?php endif ?>

        <form action="<?= ROOT_URL ?>admin/add_post_logic.php" enctype="multipart/form-data" method="POST">

            <input type="text" name="title" value="<?= $title ?>" placeholder="Title">

            <textarea rows="10" name="body" placeholder="Body"><?= $body ?></textarea>

            <div class="form_control">
                <label for="thumbnail">Add Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail">
            </div>
            <button type="submit" name="submit" class="btn">Add Post</button>

        </form>
    </div>
</section>
<!-----ADD POST ENDS---->

<?php
include __DIR__ . '/../includes/footer.php';

?>