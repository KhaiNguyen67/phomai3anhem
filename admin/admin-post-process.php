<?php
// ============================================================
// File: admin-post-process.php
// Chức năng: Xử lý lõi CRUD cho phân hệ bài viết bài (PDO)
// ============================================================
include_once 'admin-check.php';
include_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $summary = trim($_POST['summary']);
    $content = trim($_POST['content']);

    // 1. NGHIỆP VỤ: THÊM BÀI VIẾT
    if ($action === 'add') {
        $image_name = 'default-post.jpg'; // Ảnh mặc định bài viết

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'post_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/img/' . $image_name);
        }

        $sql = "INSERT INTO posts (title, slug, image, summary, content) VALUES (:title, :slug, :image, :summary, :content)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'title'   => $title,
            'slug'    => $slug,
            'image'   => $image_name,
            'summary' => $summary,
            'content' => $content
        ]);

        header("Location: admin-posts.php?status=success_add");
        exit();
    }

    // 2. NGHIỆP VỤ: SỬA BÀI VIẾT
    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $image_name = $_POST['old_image']; // Giữ ảnh cũ nếu không chọn file mới

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'post_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/img/' . $image_name);
        }

        $sql = "UPDATE posts SET title = :title, slug = :slug, image = :image, summary = :summary, content = :content WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'title'   => $title,
            'slug'    => $slug,
            'image'   => $image_name,
            'summary' => $summary,
            'content' => $content,
            'id'      => $id
        ]);

        header("Location: admin-posts.php?status=success_edit");
        exit();
    }

} elseif ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    // 3. NGHIỆP VỤ: XÓA BÀI VIẾT
    if ($action === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        header("Location: admin-posts.php?status=success_delete");
        exit();
    }
}

header("Location: admin-posts.php");
exit();
?>