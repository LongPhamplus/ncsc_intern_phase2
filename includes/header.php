<?php
require_once __DIR__ . "/../config/constants.php";

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog APP PHP & MYSQL</title>
    <!---CUSTOM CSS--->
    <link rel="stylesheet" href="<?= ROOT_URL ?>assets/css/style.css">
    <!---ICONSCOUT CDN--->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!---GOOGLE FONTS (POPPINS)--->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
</head>

<body>
    <nav>
        <div class="container nav_container">
            <a href="<?= ROOT_URL ?>" class="nav_logo">Plogplus</a>
            <ul class="nav_items">
                <li><a href="<?= ROOT_URL ?>index.php">Search</a></li>
                <li><a href="<?= ROOT_URL ?>users/manage_posts.php">Manage</a></li>
                <?php if(isset($_SESSION['user-id'])) : ?> 
                    <li class="nav_profile">
                    <div class="avatar">
                    <img src="<?=ROOT_URL . 'assets/images/' .$_SESSION['avatar']?>"> 
                    </div>
                    <ul>
                        <li><a href="<?= ROOT_URL ?>users/dashboard.php">Dashboard</a></li>
                        <li><a href="<?= ROOT_URL ?>logout.php">Log Out</a></li>
                    </ul>
                </li>
                <?php else : ?> 
             <li><a href="<?= ROOT_URL ?>login.php">Sign In</a></li> 
            <?php endif ?>
            </ul>
            <button id="open_nav-btn"><i class="uil uil-bars"></i></button>
            <button id="close_nav-btn"><i class="uil uil-times"></i></button>
        </div>
    </nav>