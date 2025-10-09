<?php
require '../config/constants.php';
require '../config/database.php';
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/middleware.php';

checkLogin();

$user_id = $_SESSION['user-id'];
$user = [
    'id' => $_SESSION['user-id'] ?? null,
    'username' => $_SESSION['username'] ?? null,
    'avatar' => $_SESSION['avatar'] ?? null,
    'role' => $_SESSION['role'] ?? null,
    'firstname' => $_SESSION['firstname'] ?? null,
    'lastname' => $_SESSION['lastname'] ?? null,
    'email' => $_SESSION['email'] ?? null,
];

if (empty($user_id)) {
    header('Location: ' . ROOT_URL . 'login.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Edit Profile</title>
    <link rel="stylesheet" href="<?= ROOT_URL ?>assets/css/style.css">
</head>

<body>
    <section class="form_section">
        <div class="container form_section-container">
            <h2>Edit Profile</h2>

            <?php if (isset($_SESSION['update-error'])) : ?>
                <div class="alert_message error">
                    <p><?= $_SESSION['update-error']; unset($_SESSION['update-error']); ?></p>
                </div>
            <?php elseif (isset($_SESSION['update-success'])) : ?>
                <div class="alert_message success">
                    <p><?= $_SESSION['update-success']; unset($_SESSION['update-success']); ?></p>
                </div>
            <?php endif; ?>

            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">

                <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" placeholder="First Name" required>
                <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" placeholder="Last Name" required>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" placeholder="Username" required>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" required>

                <div class="form_control">
                    <label for="avatar">Change Avatar</label><br>
                    <input type="file" name="avatar" id="avatar">
                </div>

                <div class="form_control">
                    <label for="password">New Password (optional)</label>
                    <input type="password" name="newpassword" placeholder="Leave blank to keep current password">
                </div>

                <button type="submit" name="submit" class="btn">Update Profile</button>
            </form>
        </div>
    </section>
</body>

</html>

<?php
include __DIR__ . '/../includes/footer.php';

?>