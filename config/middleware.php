<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function checkLogin() {
    if (empty($_SESSION['user-id'])) {
        header('Location: ' . ROOT_URL . 'login.php');
        exit();
    }
}

?>