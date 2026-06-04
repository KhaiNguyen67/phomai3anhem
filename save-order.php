<?php
// ============================================================
// File: save-order.php
// Chức năng: Lưu đơn hàng và chi tiết đơn hàng vào CSDL phomai3anhem
// ============================================================
session_start();
include_once 'config/db.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
        exit;
    }

    // Kiểm tra Giỏ hàng
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['status' => 'error', 'message' => 'Giỏ hàng trống.']);
        exit;
    }

    // Tính tổng tiền an toàn từ phía Server
    $total_money = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_money += $item['price'] * $item['quantity'];
    }

    // Lấy ID user nếu có đăng nhập
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        $pdo->beginTransaction();

        // 1. Thêm vào bảng orders theo đúng cấu trúc giữ nguyên trường cũ của bạn
        $sqlOrder = "INSERT INTO orders (user_id, full_name, email, phone, address, note, total_money, status, receiver_name, receiver_phone, receiver_address, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, NOW())";
        
        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->execute([
            $user_id,
            htmlspecialchars($data['full_name']),
            htmlspecialchars($data['email']),
            htmlspecialchars($data['phone']),
            htmlspecialchars($data['receiver_address']), // Lưu địa chỉ nhận hàng vào trường address cũ để tránh lỗi thống kê
            htmlspecialchars($data['note']),
            $total_money,
            htmlspecialchars($data['receiver_name']),
            htmlspecialchars($data['receiver_phone']),
            htmlspecialchars($data['receiver_address'])
        ]);

        $order_id = $pdo->lastInsertId();

        // 2. Thêm vào bảng chi tiết đơn hàng (order_details)
        $sqlDetail = "INSERT INTO order_details (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)";
        $stmtDetail = $pdo->prepare($sqlDetail);

        foreach ($_SESSION['cart'] as $item) {
            $stmtDetail->execute([
                $order_id,
                $item['id'],
                $item['price'],
                $item['quantity']
            ]);
        }

        $pdo->commit();

        // Làm sạch giỏ hàng sau khi đặt hàng thành công
        unset($_SESSION['cart']);

        echo json_encode(['status' => 'success', 'order_id' => $order_id]);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        exit;
    }
}