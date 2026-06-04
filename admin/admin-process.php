<?php
// ============================================================
// File: admin-process.php
// Chức năng: Tập trung xử lý lõi dữ liệu CRUD bằng đối tượng PDO
//            Xử lý bổ sung trường thông tin khuyến mãi: sale_price & is_on_sale
// ============================================================

include_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    // Lấy và chuẩn hóa dữ liệu đầu vào cơ bản
    $name = trim($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    
    // Nhận dữ liệu giá KM (nếu trống thì gán NULL)
    $sale_price = ($_POST['sale_price'] !== '') ? (float)$_POST['sale_price'] : null;
    
    $short_desc = trim($_POST['short_desc']);
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // --------------------------------------------------------
    // NGHIỆP VỤ 1: THÊM MỚI SẢN PHẨM
    // --------------------------------------------------------
    if ($action === 'add') {
        $image_name = 'default.jpg'; 

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'cheese_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/img/' . $image_name);
        }

        $sql = "INSERT INTO products (name, category_id, price, sale_price, image, short_desc, is_on_sale, is_featured) 
                VALUES (:name, :category_id, :price, :sale_price, :image, :short_desc, :is_on_sale, :is_featured)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name'        => $name,
            'category_id' => $category_id,
            'price'       => $price,
            'sale_price'  => $sale_price,
            'image'       => $image_name,
            'short_desc'  => $short_desc,
            'is_on_sale'  => $is_on_sale,
            'is_featured' => $is_featured
        ]);

        header("Location: admin-products.php?status=success_add");
        exit();
    }

    // --------------------------------------------------------
    // NGHIỆP VỤ 2: CẬP NHẬT CHỈNH SỬA SẢN PHẨM
    // --------------------------------------------------------
    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $image_name = $_POST['old_image']; 

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'cheese_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/img/' . $image_name);
        }

        $sql = "UPDATE products SET 
                    name = :name, 
                    category_id = :category_id, 
                    price = :price, 
                    sale_price = :sale_price, 
                    image = :image, 
                    short_desc = :short_desc, 
                    is_on_sale = :is_on_sale, 
                    is_featured = :is_featured 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name'        => $name,
            'category_id' => $category_id,
            'price'       => $price,
            'sale_price'  => $sale_price,
            'image'       => $image_name,
            'short_desc'  => $short_desc,
            'is_on_sale'  => $is_on_sale,
            'is_featured' => $is_featured,
            'id'          => $id
        ]);

        header("Location: admin-products.php?status=success_edit");
        exit();
    }

} elseif ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    // --------------------------------------------------------
    // NGHIỆP VỤ 3: XÓA SẢN PHẨM KHỎI DATABASE
    // --------------------------------------------------------
    if ($action === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        header("Location: admin-products.php?status=success_delete");
        exit();
    }
}

header("Location: admin-products.php");
exit();
?>