<?php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . '/../config/middleware.php';
require_once __DIR__ . '/../config/database.php';

checkLogin();



?>


<!----------Manage Users------->
<section class="dashboard">
    <!--//ADD POST SUCCESS MESSAGE-->
    <?php if (isset($_SESSION['add-post-success'])) : //shows if add post  was successful 
    ?>
        <div class="alert_message success container">
            <p>
                <?= $_SESSION['add-post-success'];
                //DELETE AFER EXECUTING
                unset($_SESSION['add-post-success']);
                ?>
            </p>
        </div>
        <!--//EDIT POST SUCCESS MESSAGE-->
    <?php elseif (isset($_SESSION['edit-post-success'])) : //shows if edit post  was successful 
    ?>
        <div class="alert_message success container">
            <p>
                <?= $_SESSION['edit-post-success'];
                //DELETE AFER EXECUTING
                unset($_SESSION['edit-post-success']);
                ?>
            </p>
        </div>

        <!--//EDIT POST ERROR MESSAGE-->
    <?php elseif (isset($_SESSION['edit-post'])) : //shows if edit post  was NOT successful 
    ?>
        <div class="alert_message error container">
            <p>
                <?= $_SESSION['edit-post'];
                //DELETE AFER EXECUTING
                unset($_SESSION['edit-post']);
                ?>
            </p>
        </div>

        <!--//DELETE POST SUCCESS MESSAGE-->
    <?php elseif (isset($_SESSION['delete-post-success'])) : //shows if delete post  was successful 
    ?>
        <div class="alert_message success container">
            <p>
                <?= $_SESSION['delete-post-success'];
                //DELETE AFER EXECUTING
                unset($_SESSION['delete-post-success']);
                ?>
            </p>
        </div>
    <?php endif ?>

    <div class="container dashboard_container">
        <button id="show_sidebar-btn" class="sidebar_toogle">
            <i class="uil uil-angle-right-b"></i></button>
        <button id="hide_sidebar-btn" class="sidebar_toogle">
            <i class="uil uil-angle-left-b"></i></button>
        <aside>
            <ul>
                <li><a href="add_post.php"><i class="uil uil-pen"></i>
                        <h5>Add Post</h5>
                    </a></li>

                <li><a href="manage_posts.php" class="active"><i class="uil uil-create-dashboard"></i>
                        <h5>Manage Post</h5>
                    </a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>


                    <li><a href="add_user.php"><i class="uil uil-user-plus"></i>
                            <h5>Add User</h5>
                        </a></li>

                    <li><a href="manage_users.php"><i class="uil uil-users-alt"></i>
                            <h5>Manage User</h5>
                        </a></li>

                <?php endif ?>
            </ul>
        </aside>

        <main>
            <h2>Manage Posts</h2>
            <?php
            $sql = "SELECT * FROM posts WHERE user_id = :user_id ORDER BY id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $_SESSION['user-id']);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ?>
            <!--IF NO POSTS FOUND-->
            <?php if (empty($posts)) : ?>
                <div class="alert_message error"><?= "No posts found" ?></div>
            <?php else : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--LOOP THROUGH AND DISPLAY POSTS-->
                            <!--GET CATEGORY TITLE OF ECAH POST FROM CATEGORIES TABLE-->

                        <?php foreach($posts as $post) : ?>
                            <tr>
                                <td><?= $post['title']?></td>
                                <td><a href="<?= ROOT_URL ?>users/edit_post.php?id=<?= $post['id']?>" class="btn sm">Edit</a></td>
                                <td><a href="<?= ROOT_URL ?>users/delete_post.php?id=<?= $post['id']?>" class="btn sm danger">Delete</a></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <!--DISPLAY IF NO POSTS FOUND-->
            <?php endif ?>
        </main>
    </div>
</section>

<!-----Manage Categories Ends--------->

<?php
include __DIR__ . '/../includes/footer.php';

?>