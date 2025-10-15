<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/middleware.php';
include __DIR__ . '/../includes/header.php';

checkLogin();

// Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['edit-user'] = 'You are not authorized to edit users.';
    header('Location: ' . ROOT_URL . 'users/manage_posts.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle update
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($id <= 0 || $firstname === '' || $lastname === '' || $username === '' || $email === '' || !in_array($role, ['user','admin'], true)) {
        $_SESSION['edit-user'] = 'Please fill in all required fields.';
        $_SESSION['edit-user-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'users/edit_user.php?id=' . $id);
        exit;
    }
    if ($password !== '' && $password !== $confirmPassword) {
        $_SESSION['edit-user'] = 'Passwords do not match.';
        $_SESSION['edit-user-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'users/edit_user.php?id=' . $id);
        exit;
    }

    try {
        // Ensure username/email unique for other users
        $chk = $conn->prepare('SELECT id FROM users WHERE (username = :username OR email = :email) AND id <> :id LIMIT 1');
        $chk->execute([':username' => $username, ':email' => $email, ':id' => $id]);
        if ($chk->fetch()) {
            $_SESSION['edit-user'] = 'Username or email already in use.';
            $_SESSION['edit-user-data'] = $_POST;
            header('Location: ' . ROOT_URL . 'users/edit_user.php?id=' . $id);
            exit;
        }

        if ($password !== '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = 'UPDATE users 
                SET firstname = :firstname, lastname = :lastname, username = :username, 
                    email = :email, role = :role, password = :password 
                WHERE id = :id';
        $params = [
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':username' => $username,
            ':email' => $email,
            ':role' => $role,
            ':password' => $hashedPassword,
            ':id' => $id,
        ];
        } else {
            $sql = 'UPDATE users 
                    SET firstname = :firstname, lastname = :lastname, username = :username, 
                        email = :email, role = :role 
                    WHERE id = :id';
            $params = [
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':username' => $username,
                ':email' => $email,
                ':role' => $role,
                ':id' => $id,
            ];
        }

        $upd = $conn->prepare($sql);
        $upd->execute($params);



        $_SESSION['edit-user-success'] = 'User updated successfully.';
        header('Location: ' . ROOT_URL . 'users/manage_users.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['edit-user'] = 'Failed to update user.';
        $_SESSION['edit-user-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'users/edit_user.php?id=' . $id);
        exit;
    }
}

// Load user for form (GET)
if ($id <= 0) {
    $_SESSION['edit-user'] = 'Invalid user id.';
    header('Location: ' . ROOT_URL . 'users/manage_users.php');
    exit;
}

try {
    $stmt = $conn->prepare('SELECT id, firstname, lastname, username, email, role FROM users WHERE id = :id LIMIT 1');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $_SESSION['edit-user'] = 'User not found.';
        header('Location: ' . ROOT_URL . 'users/manage_users.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['edit-user'] = 'Failed to load user.';
    header('Location: ' . ROOT_URL . 'users/manage_users.php');
    exit;
}

// If we had previous input due to error, repopulate
if (isset($_SESSION['edit-user-data'])) {
    $user = array_merge($user, $_SESSION['edit-user-data']);
    unset($_SESSION['edit-user-data']);
}
?>

<section class="form_section">
    <div class="container form_section-container">
        <h2>Edit User</h2>
        <?php if (isset($_SESSION['edit-user'])) : ?>
            <div class="alert_message error">
                <p><?= htmlspecialchars($_SESSION['edit-user']); unset($_SESSION['edit-user']); ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= ROOT_URL ?>users/edit_user.php?id=<?= (int)$user['id'] ?>" method="POST">
            <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">

            <input type="text" name="firstname" placeholder="First name" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>" required>
            <input type="text" name="lastname" placeholder="Last name" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>" required>
            <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
            <input type="password" name="confirm_password" placeholder="Confirm New Password (leave blank to keep current)">
            <label for="role">Role</label>
            <select name="role" id="role">
                <option value="user" <?= (isset($user['role']) && $user['role'] === 'user') ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= (isset($user['role']) && $user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit" class="btn">Save Changes</button>
            <a href="<?= ROOT_URL ?>users/manage_users.php" class="btn sm">Cancel</a>
        </form>

        <form action="<?= ROOT_URL ?>users/delete_user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.');" style="margin-top:1rem;">
            <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
            <button type="submit" class="btn danger">Delete User</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
