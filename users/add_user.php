<?php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . '/../config/middleware.php';

checkLogin();


$firstname = $_SESSION['add-user-data']['firstname'] ?? null;
$lastname = $_SESSION['add-user-data']['lastname'] ?? null;
$username = $_SESSION['add-user-data']['username'] ?? null;
$email = $_SESSION['add-user-data']['email'] ?? null;

unset($_SESSION['add-user-data']);
?>



<!-----ADD USER----->
<section class="form_section">
    <div class="container form_section-container">
        <h2>Add User</h2>

         <!----PASS THE ERROR MESSAGES FROM add-user_logic.php---->
         <?php if (isset($_SESSION['add-user'])) : ?>
                <div class="alert_message error">
                    <p>
                   <?= $_SESSION['add-user'];
                    //DELETE AFER EXECUTING
                    unset($_SESSION['add-user']);
                        ?>
                    </p>
                </div>
            <?php endif ?>

        <form action="<?= ROOT_URL ?>users/add_user_logic.php" enctype="multipart/form-data" method="POST">
            <input type="text" name="firstname" value="<?= $firstname ?>" placeholder="First Name">
            <input type="text" name="lastname" value="<?= $lastname ?>" placeholder="Last Name">
            <input type="text" name="username" value="<?= $username ?>" placeholder="Username">
            <input type="email" name="email" value="<?= $email ?>" placeholder="Email">
            <input type="password" name="createpassword"  placeholder="Create Password">
            <input type="password" name="confirmpassword" placeholder="Confirm Password">
            <select name="userrole">
                <option value="0">Author</option>
                <option value="1">Admin</option>
            </select>


            <div class="form_control">
                <label for="avatar">User Avatar</label>
                <input type="file" name="avatar" id="avatar">
            </div>
            <button type="submit" name="submit" class="btn">Add User</button>
        </form>


    </div>
</section>
<!-----ADD USER ENDS---->

<?php
include __DIR__ . '/../includes/footer.php';

?>