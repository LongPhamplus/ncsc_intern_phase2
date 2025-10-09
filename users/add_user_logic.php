<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/middleware.php';

checkLogin();

// Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['add-user'] = 'You are not authorized to add users.';
    header('Location: ' . ROOT_URL . 'users/manage_posts.php');
    exit;
}

if (!isset($_POST['submit'])) {
    header('Location: ' . ROOT_URL . 'users/add_user.php');
    exit;
}

$firstname = trim($_POST['firstname'] ?? '');
$lastname  = trim($_POST['lastname'] ?? '');
$username  = trim($_POST['username'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['createpassword'] ?? '';
$confirm   = $_POST['confirmpassword'] ?? '';
// Legacy select uses userrole with values 0/1; map to role string
$userrole  = $_POST['userrole'] ?? '0';
$role      = ($userrole === '1' || strtolower($userrole) === 'admin') ? 'admin' : 'user';

// Preserve form data on error (except passwords)
$_SESSION['add-user-data'] = [
    'firstname' => $firstname,
    'lastname'  => $lastname,
    'username'  => $username,
    'email'     => $email
];

// Basic validation
if ($firstname === '' || $lastname === '' || $username === '' || $email === '' || $password === '' || $confirm === '') {
    $_SESSION['add-user'] = 'Please fill in all required fields.';
    header('Location: ' . ROOT_URL . 'users/add_user.php');
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['add-user'] = 'Invalid email address.';
    header('Location: ' . ROOT_URL . 'users/add_user.php');
    exit;
}
if ($password !== $confirm) {
    $_SESSION['add-user'] = 'Passwords do not match.';
    header('Location: ' . ROOT_URL . 'users/add_user.php');
    exit;
}
if (strlen($password) < 6) {
    $_SESSION['add-user'] = 'Password must be at least 6 characters.';
    header('Location: ' . ROOT_URL . 'users/add_user.php');
    exit;
}

// Check duplicates
try {
    $chk = $conn->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
    $chk->execute([':username' => $username, ':email' => $email]);
    if ($chk->fetch()) {
        $_SESSION['add-user'] = 'Username or email already exists.';
        header('Location: ' . ROOT_URL . 'users/add_user.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['add-user'] = 'Failed to validate user uniqueness.';
    header('Location: ' . ROOT_URL . 'users/add_user.php');
    exit;
}

// Handle avatar upload
$avatarFile = 'default.png';
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $tmpPath = $_FILES['avatar']['tmp_name'];
        $orig    = $_FILES['avatar']['name'];
        $ext     = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array($ext, $allowed, true)) {
            $_SESSION['add-user'] = 'Invalid avatar format (allowed: jpg, jpeg, png, gif).';
            header('Location: ' . ROOT_URL . 'users/add_user.php');
            exit;
        }
        $newName = uniqid('ava_', true) . '.' . $ext;
        $destDir = __DIR__ . '/../assets/images/';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }
        $dest = $destDir . $newName;
        if (!move_uploaded_file($tmpPath, $dest)) {
            $_SESSION['add-user'] = 'Failed to upload avatar.';
            header('Location: ' . ROOT_URL . 'users/add_user.php');
            exit;
        }
        $avatarFile = $newName;
    } else {
        $_SESSION['add-user'] = 'Error uploading avatar.';
        header('Location: ' . ROOT_URL . 'users/add_user.php');
        exit;
    }
}

// Insert user
try {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $conn->prepare('INSERT INTO users (firstname, lastname, username, email, password, role, avatar) VALUES (:firstname, :lastname, :username, :email, :password, :role, :avatar)');
    $ins->execute([
        ':firstname' => $firstname,
        ':lastname'  => $lastname,
        ':username'  => $username,
        ':email'     => $email,
        ':password'  => $hash,
        ':role'      => $role,
        ':avatar'    => $avatarFile
    ]);

    unset($_SESSION['add-user-data']);
    $_SESSION['add-user-success'] = 'User added successfully.';
    header('Location: ' . ROOT_URL . 'users/manage_users.php');
    exit;
} catch (Exception $e) {
    $_SESSION['add-user'] = 'Failed to add user.';
    header('Location: ' . ROOT_URL . 'users/add_user.php');
    exit;
}
