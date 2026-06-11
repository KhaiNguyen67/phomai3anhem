<?php
// ============================================================
// File: admin/update-order-status.php
// Chức năng: Cập nhật trạng thái đơn hàng & Tự động trừ kho khi thành công
// ============================================================
session_start();
// Điều chỉnh đường dẫn include config tùy thuộc vào cấu trúc thư mục admin của bạn
include_once '../config/db.php'; 

header('Content-Type: application/json; charset=utf-8');

// Chặn nếu không phải phương thức POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức yêu cầu không hợp lệ.']);
    exit();
}

// Lấy dữ liệu từ form hoặc từ payload AJAX
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$new_status = isset($_POST['status']) ? trim(htmlspecialchars($_POST['status'])) : '';

if ($order_id <= 0 || empty($new_status)) {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu đầu vào không hợp lệ.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Lấy trạng thái hiện tại của đơn hàng trước khi update để tránh trừ kho 2 lần
    $stmtCheck = $pdo->prepare("SELECT status FROM orders WHERE id = ?");
    $stmtCheck->execute([$order_id]);
    $current_order = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$current_order) {
        echo json_encode(['status' => 'error', 'message' => 'Đơn hàng không tồn tại.']);
        $pdo->rollBack();
        exit();
    }

    $old_status = $current_order['status'];

    // 2. Tiến hành cập nhật trạng thái mới cho đơn hàng
    $stmtUpdate = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmtUpdate->execute([$new_status, $order_id]);

    // 3. LOGIC TRỪ SỐ LƯỢNG KHO: Chỉ trừ kho khi đơn hàng chuyển từ trạng thái khác sang 'success'
    if ($new_status === 'success' && $old_status !== 'success') {
        
        // Lấy chi tiết các sản phẩm trong đơn hàng từ bảng order_details
        $stmtDetails = $pdo->prepare("SELECT product_id, quantity FROM order_details WHERE order_id = ?");
        $stmtDetails->execute([$order_id]);
        $order_items = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

        // Chuẩn bị câu lệnh cập nhật giảm số lượng (Trường trong DB của bạn là quantity)
        $stmtMinusStock = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");

        foreach ($order_items as $item) {
            // Thực thi trừ kho trực tiếp
            $stmtMinusStock->execute([
                $item['quantity'],   // Số lượng khách mua
                $item['product_id'], // ID sản phẩm phô mai
                $item['quantity']    // Chặn điều kiện để tránh trường hợp số lượng bị âm (vượt quá tồn kho)
            ]);
        }
    }
    
    // LOGIC HOÀN KHO (Tùy chọn mở rộng): Nếu đơn hàng đang 'success' (đã trừ kho) mà bị Admin đổi ngược thành 'canceled' (Hủy đơn)
    if ($new_status === 'canceled' && $old_status === 'success') {
        $stmtDetails = $pdo->prepare("SELECT product_id, quantity FROM order_details WHERE order_id = ?");
        $stmtDetails->execute([$order_id]);
        $order_items = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

        $stmtPlusStock = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        foreach ($order_items as $item) {
            $stmtPlusStock->execute([$item['quantity'], $item['product_id']]);
        }
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Cập nhật trạng thái đơn hàng và đồng bộ kho thành công!']);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống khi xử lý đơn hàng: ' . $e->getMessage()]);
    exit();
}