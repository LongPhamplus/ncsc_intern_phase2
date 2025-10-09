<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/middleware.php';
checkLogin();

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user-id']; 
    $title = trim($_POST['title']);
    $content = trim($_POST['body']); 
    $thumbnail = 'default.jpg';

    if (empty($title) || empty($content)) {
        $_SESSION['add-post-error'] = "Vui lòng nhập đầy đủ tiêu đề và nội dung!";
        $_SESSION['form_data'] = ['title' => $title, 'body' => $content];
        header('Location: ' . ROOT_URL . 'users/add_post.php');
        exit;
    }

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['thumbnail']['tmp_name'];
        $fileName = uniqid() . '_' . basename($_FILES['thumbnail']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExt, $allowed)) {
            $uploadDir = __DIR__ . '/../assets/images/thumbnail/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filePath = $uploadDir . $fileName;
            if (move_uploaded_file($fileTmp, $filePath)) {
                $thumbnail = $fileName;
            } else {
                $_SESSION['add-post-error'] = "Không thể tải lên ảnh đại diện.";
                header('Location: ' . ROOT_URL . 'users/add_post.php');
                exit;
            }
        } else {
            $_SESSION['add-post-error'] = "Định dạng ảnh không hợp lệ (chỉ JPG, PNG, GIF).";
            header('Location: ' . ROOT_URL . 'users/add_post.php');
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, thumbnail, created_at)
                        VALUES (?, ?, ?, ?, NOW())");

    if ($stmt->execute([$user_id, $title, $content, $thumbnail])) {
        $_SESSION['add-post-success'] = "Bài viết đã được thêm thành công!";
        header('Location: ' . ROOT_URL . 'users/manage_posts.php');
        exit;
    } else {
        $_SESSION['add-post-error'] = "Lỗi khi thêm bài viết: ";
        header('Location: ' . ROOT_URL . 'users/add_post.php');
        exit;
    }
}

$conn->close();
?>
