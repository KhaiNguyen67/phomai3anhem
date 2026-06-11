<?php
// ============================================================
// File: admin-process.php
// Chức năng: Tập trung xử lý lõi dữ liệu CRUD bằng đối tượng PDO
//            Xử lý bổ sung trường thông tin khuyến mãi: sale_price & is_on_sale
//            MỚI: Xử lý lưu thông tin số lượng kho (stock) khi thêm/sửa
//            MỚI: Đón nhận và xử lý tăng/giảm nhanh qua AJAX (JSON response)
// ============================================================

include_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    // --------------------------------------------------------
    // NGHIỆP VỤ ĐẶC BIỆT: CẬP NHẬT SỐ LƯỢNG KHO TRỰC TIẾP QUA AJAX
    // (Xử lý ưu tiên hàng đầu, trả về cấu trúc JSON và ngắt luồng ngay lập tức)
    // --------------------------------------------------------
    if ($action === 'update_stock_ajax') {
        header('Content-Type: application/json');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

        if ($id <= 0 || $stock < 0) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu đầu vào không hợp lệ.']);
            exit();
        }

        try {
            $sql = "UPDATE products SET stock = :stock WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute(['stock' => $stock, 'id' => $id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật kho thành công.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật cơ sở dữ liệu.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL: ' . $e->getMessage()]);
        }
        exit(); // Kết thúc kịch bản xử lý AJAX
    }

    // Lấy và chuẩn hóa dữ liệu đầu vào cơ bản cho Form chuẩn (add / edit)
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    
    // MỚI: Nhận dữ liệu số lượng kho từ Form (mặc định bằng 0 nếu trống)
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    
    // Nhận dữ liệu giá KM (nếu trống thì gán NULL)
    $sale_price = (isset($_POST['sale_price']) && $_POST['sale_price'] !== '') ? (float)$_POST['sale_price'] : null;
    
    $short_desc = trim($_POST['short_desc'] ?? '');
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // --------------------------------------------------------
    // NGHIỆP VỤ 1: THÊM MỚI SẢN PHẨM (Bao gồm trường stock)
    // --------------------------------------------------------
    if ($action === 'add') {
        $image_name = 'default.jpg'; 

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'cheese_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/img/' . $image_name);
        }

        // Đã tích hợp bổ sung trường `stock` vào câu lệnh INSERT
        $sql = "INSERT INTO products (name, category_id, price, sale_price, stock, image, short_desc, is_on_sale, is_featured) 
                VALUES (:name, :category_id, :price, :sale_price, :stock, :image, :short_desc, :is_on_sale, :is_featured)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name'        => $name,
            'category_id' => $category_id,
            'price'       => $price,
            'sale_price'  => $sale_price,
            'stock'       => $stock, // Mới
            'image'       => $image_name,
            'short_desc'  => $short_desc,
            'is_on_sale'  => $is_on_sale,
            'is_featured' => $is_featured
        ]);

        header("Location: admin-products.php?status=success_add");
        exit();
    }

    // --------------------------------------------------------
    // NGHIỆP VỤ 2: CẬP NHẬT CHỈNH SỬA SẢN PHẨM (Bao gồm trường stock)
    // --------------------------------------------------------
    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $image_name = $_POST['old_image'] ?? 'default.jpg'; 

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'cheese_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/img/' . $image_name);
        }

        // Đã tích hợp bổ sung sửa trường `stock` vào câu lệnh UPDATE
        $sql = "UPDATE products SET 
                    name = :name, 
                    category_id = :category_id, 
                    price = :price, 
                    sale_price = :sale_price, 
                    stock = :stock, 
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
            'stock'       => $stock, // Mới
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

// Chặn các truy cập không hợp lệ quay ngược lại danh sách quản trị
header("Location: admin-products.php");
exit();
?>