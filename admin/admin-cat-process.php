<?php
// ============================================================
// File: admin-cat-process.php
// Chức năng: Xử lý thêm, sửa, xóa danh mục (PDO)
// ============================================================
include_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name']);

    // 1. Nghiệp vụ: THÊM DANH MỤC
    if ($action === 'add' && !empty($name)) {
        $sql = "INSERT INTO categories (name) VALUES (:name)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name]);

        header("Location: admin-categories.php?status=success_add");
        exit();
    }

    // 2. Nghiệp vụ: SỬA DANH MỤC
    if ($action === 'edit' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'id' => $id
        ]);

        header("Location: admin-categories.php?status=success_edit");
        exit();
    }

} elseif ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    // 3. Nghiệp vụ: XÓA DANH MỤC (Có kiểm tra ràng buộc sản phẩm)
    if ($action === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        // BẢO VỆ DỮ LIỆU: Kiểm tra xem có sản phẩm nào đang thuộc danh mục này không
        $check_sql = "SELECT COUNT(*) FROM products WHERE category_id = :id";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute(['id' => $id]);
        $has_products = $check_stmt->fetchColumn();

        if ($has_products > 0) {
            // Nếu có sản phẩm, không cho xóa và trả về thông báo lỗi
            header("Location: admin-categories.php?status=error_has_products");
            exit();
        } else {
            // Nếu trống, tiến hành xóa an toàn
            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            header("Location: admin-categories.php?status=success_delete");
            exit();
        }
    }
}

header("Location: admin-categories.php");
exit();
?>