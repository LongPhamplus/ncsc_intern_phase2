<?php
require 'config/constants.php';
include 'config/database.php';

if (isset($_POST['submit'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $createpassword = $_POST['createpassword'];
    $confirmpassword = $_POST['confirmpassword'];
    $avatar = $_FILES['avatar'];

    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($createpassword) || empty($confirmpassword)) {
        $_SESSION['signup-data'] = $_POST;
        $_SESSION['signup-error'] = "Please fill in all fields";
        header('location: ' . ROOT_URL . 'register.php');
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['signup-data'] = $_POST;
        $_SESSION['signup-error'] = "Please enter a valid email";
        header('location: ' . ROOT_URL . 'register.php');
        exit();
    } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['signup-data'] = $_POST;
        $_SESSION['signup-error'] = "Password should be at least 8 characters long";
        header('location: ' . ROOT_URL . 'register.php');
        exit();
    } elseif ($createpassword !== $confirmpassword) {
        $_SESSION['signup-data'] = $_POST;
        $_SESSION['signup-error'] = "Passwords do not match";
        header('location: ' . ROOT_URL . 'register.php');
        exit();
    } else {
        $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['signup-data'] = $_POST;
            $_SESSION['signup-error'] = "Username or Email already exists";
            header('location: ' . ROOT_URL . 'register.php');
            exit();
        } else {
            if ($avatar['name']) {
                $avatar_name = $avatar['name'];
                $avatar_tmp_name = $avatar['tmp_name'];
                $avatar_size = $avatar['size'];
                $avatar_error = $avatar['error'];

                $allowed_extensions = ['png', 'jpg', 'jpeg'];
                $extension = pathinfo($avatar_name, PATHINFO_EXTENSION);
                if (!in_array(strtolower($extension), $allowed_extensions)) {
                    $_SESSION['signup-data'] = $_POST;
                    $_SESSION['signup-error'] = "Invalid file type. Only PNG, JPG, and JPEG are allowed.";
                    header('location: ' . ROOT_URL . 'register.php');
                    exit();
                }

                if ($avatar_size > 2 * 1024 * 1024) {
                    $_SESSION['signup-data'] = $_POST;
                    $_SESSION['signup-error'] = "File size exceeds the maximum limit of 2MB.";
                    header('location: ' . ROOT_URL . 'register.php');
                    exit();
                }

                $new_avatar_name = uniqid() . '.' . $extension;
                $avatar_destination_path = 'assets/images/' . $new_avatar_name;

                if (!move_uploaded_file($avatar_tmp_name, $avatar_destination_path)) {
                    $_SESSION['signup-data'] = $_POST;
                    $_SESSION['signup-error'] = "Failed to upload avatar. Please try again.";
                    header('location: ' . ROOT_URL . 'register.php');
                    exit();
                }
            } else {
                $new_avatar_name = 'default.jpg';
            }

            $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, email, password, avatar) VALUES (:firstname, :lastname, :username, :email, :password, :avatar)");
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':avatar', $new_avatar_name);
            if ($stmt->execute()) {
                $_SESSION['signup-success'] = "Registration successful. Please log in.";
                header('location: ' . ROOT_URL . 'login.php');
                exit();
            } else {
                $_SESSION['signup-data'] = $_POST;
                $_SESSION['signup-error'] = "An error occurred during registration. Please try again.";
                header('location: ' . ROOT_URL . 'register.php');
                exit();
            }

        }
    }

}
?>