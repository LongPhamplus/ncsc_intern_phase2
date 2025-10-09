<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/middleware.php';

checkLogin();

if (isset($_POST['submit'])) {
    $postId = $_POST['id'] ?? null;
    $user_id = $_SESSION['user-id'] ?? null;
    $title = trim($_POST['title']);
    $content = trim($_POST['body']);
    $oldThumbnail = $_POST['previous_thumbnail_name'] ?? 'default.jpg';
    $newThumbnail = $oldThumbnail;

    // Kiểm tra dữ liệu đầu vào
    if (!$postId || !$user_id || empty($title) || empty($content)) {
        $_SESSION['edit-post-error'] = "Vui lòng nhập đầy đủ tiêu đề và nội dung.";
        header('Location: ' . ROOT_URL . 'users/edit_post.php?id=' . $postId);
        exit;
    }

    // Kiểm tra xem bài viết có thuộc về user hiện tại không
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, $user_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        $_SESSION['edit-post-error'] = "Bạn không có quyền chỉnh sửa bài viết này.";
        header('Location: ' . ROOT_URL . 'users/manage_posts.php');
        exit;
    }

    // Xử lý upload ảnh (nếu có)
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
                $newThumbnail = $fileName;

                // Xóa ảnh cũ nếu có và khác default
                $oldPath = $uploadDir . $oldThumbnail;
                if (file_exists($oldPath) && $oldThumbnail !== 'default.jpg') {
                    unlink($oldPath);
                }
            } else {
                $_SESSION['edit-post-error'] = "Không thể tải lên ảnh mới.";
                header('Location: ' . ROOT_URL . 'users/edit_post.php?id=' . $postId);
                exit;
            }
        } else {
            $_SESSION['edit-post-error'] = "Định dạng ảnh không hợp lệ (chỉ JPG, PNG, GIF).";
            header('Location: ' . ROOT_URL . 'users/edit_post.php?id=' . $postId);
            exit;
        }
    }

    // Cập nhật dữ liệu bài viết
    $stmt = $conn->prepare("UPDATE posts 
                            SET title = ?, content = ?, thumbnail = ?, updated_at = NOW() 
                            WHERE id = ? AND user_id = ?");
    $success = $stmt->execute([$title, $content, $newThumbnail, $postId, $user_id]);

    if ($success) {
        $_SESSION['edit-post-success'] = "Cập nhật bài viết thành công!";
        header('Location: ' . ROOT_URL . 'users/manage_posts.php');
        exit;
    } else {
        $_SESSION['edit-post-error'] = "Lỗi khi cập nhật bài viết.";
        header('Location: ' . ROOT_URL . 'users/edit_post.php?id=' . $postId);
        exit;
    }
}

$conn = null; // đóng kết nối
?>
