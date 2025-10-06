<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config/constants.php';
include 'config/database.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['signin-data'] = $_POST;
        $_SESSION['signin-error'] = "Please fill in all fields.";
        header('Location: ' . ROOT_URL . 'login.php');
        exit();
    }
    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user-id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['avatar'] = $user['avatar'];

            // Redirect to dashboard
            header('Location: ' . ROOT_URL . 'users/manage_posts.php');
            exit();
        } else {
            $_SESSION['signin-data'] = $_POST;
            $_SESSION['signin-error'] = "Invalid username or password.";
            header('Location: ' . ROOT_URL . 'login.php');
            exit();
        }
    } else {
        $_SESSION['signin-data'] = $_POST;
        $_SESSION['signin-error'] = "Invalid username or password.";
        header('Location: ' . ROOT_URL . 'login.php');
        exit();
    }
}
?>
