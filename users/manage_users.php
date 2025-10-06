<?php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . "/../config/constants.php";

$_SESSION['user_is_admin'] = 1;

?>

<!----------Manage Users------->
<section class="dashboard">

    <!----PASS THE SUCCESS MESSAGE-FROM add-user-logic.php--->


    <?php if (isset($_SESSION['add-user-success'])) : //shows if add user was successful 
    ?>
        <div class="alert_message success container">
            <p>
                <?= $_SESSION['add-user-success'];
                //DELETE AFER EXECUTING
                unset($_SESSION['add-user-success']);
                ?>
            </p>
        </div>


        <!--//EDIT USER SUCCESS MESSAGE-->
    <?php elseif (isset($_SESSION['edit-user-success'])) : //shows if edit user was successful 
    ?>
        <div class="alert_message success container">
            <p>
                <?= $_SESSION['edit-user-success'];
                //DELETE AFER EXECUTING
                unset($_SESSION['edit-user-success']);
                ?>
            </p>
        </div>
        <!--//EDIT USER ERROR MESSAGE-->
    <?php elseif (isset($_SESSION['edit-user'])) : //shows if edit user was NOT successful 
    ?>
        <div class="alert_message error container">
            <p>
                <?= $_SESSION['edit-user'];
                //DELETE AFER EXECUTING
                unset($_SESSION['edit-user']);
                ?>
            </p>
        </div>

         <!--//DELETE USER ERROR MESSAGE-->
    <?php elseif (isset($_SESSION['delete-user'])) : //shows if delete user was NOT successful 
    ?>
        <div class="alert_message error container">
            <p>
                <?= $_SESSION['delete-user'];
                //DELETE AFER EXECUTING
                unset($_SESSION['delete-user']);
                ?>
            </p>
        </div>
                <!--//DELETE USER SUCCESS MESSAGE-->
    <?php elseif (isset($_SESSION['delete-user-success'])) : //shows if delete user was successful 
    ?>
        <div class="alert_message success container">
            <p>
                <?= $_SESSION['delete-user-success'];
                //DELETE AFER EXECUTING
                unset($_SESSION['delete-user-success']);
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

                <li><a href="manage_posts.php"><i class="uil uil-create-dashboard"></i>
                        <h5>Manage Post</h5>
                    </a></li>
        
                <?php if (isset($_SESSION['user_is_admin'])) : ?>


                    <li><a href="add_user.php"><i class="uil uil-user-plus"></i>
                            <h5>Add User</h5>
                        </a></li>

                    <li><a href="manage_users.php" class="active"><i class="uil uil-users-alt"></i>
                            <h5>Manage User</h5>
                        </a></li>

                <?php endif ?>

            </ul>
        </aside>

        <main>
            <h2>Manage Users</h2>
            <!--IF NO USER FOUND-->
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Edit</th>
                        <th>Delete</th>
                        <th>Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <!--LOOP THROUGH AND DISPLAY USERS-->

                        <tr>
                            <td><?= "{$user['firstname']} {$user['lastname']}" ?></td> <!--GET USER NAMES-->
                            <td><?= $user['username'] ?></td> <!--GET USERNAME-->
                            <td><a href="<?= ROOT_URL ?>admin/edit_user.php?id=<?= $user['id'] ?>" class="btn sm">Edit</a></td> <!--GET ID/EDIT USER-->
                            <td><a href="<?= ROOT_URL ?>admin/delete_user.php?id=<?= $user['id'] ?>" class="btn sm danger">Delete</a></td> <!--DELETE USER-->
                            <td><?= $user['is_admin'] ? 'Yes' : 'No' ?></td> <!--CHECK IF AUTHOR & ADMIN-->
                        </tr>

                </tbody>
            </table>
            <!--DISPLAY IF NO USER FOUND-->
                <div class="alert_message error"><?= "No users found" ?></div>
        </main>
    </div>
</section>

<!-----Manage Categories Ends--------->

<?php
include __DIR__ . '/../includes/footer.php';

?>