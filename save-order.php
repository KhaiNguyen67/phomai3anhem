<?php
// ============================================================
// File: save-order.php
// Chức năng: Lưu đơn hàng - Chỉ trừ kho ngay nếu là COD
// ============================================================
session_start();
include_once 'config/db.php'; 

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
        exit;
    }

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['status' => 'error', 'message' => 'Giỏ hàng của bạn đang trống.']);
        exit;
    }

    // Tính tổng tiền an toàn từ Server
    $total_money = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_money += $item['price'] * $item['quantity'];
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $payment_method = isset($data['payment_method']) ? htmlspecialchars($data['payment_method']) : 'cod';
    
    // Đặt trạng thái ban đầu
    $status = ($payment_method === 'cod') ? 'pending' : 'unpaid';

    try {
        $pdo->beginTransaction();

        // 1. Ghi dữ liệu vào bảng orders
        $sqlOrder = "INSERT INTO orders (user_id, full_name, email, phone, address, note, total_money, payment_method, status, receiver_name, receiver_phone, receiver_address, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->execute([
            $user_id,
            htmlspecialchars($data['full_name'] ?? ''),
            htmlspecialchars($data['email'] ?? ''),
            htmlspecialchars($data['phone'] ?? ''),
            htmlspecialchars($data['receiver_address'] ?? ''), 
            htmlspecialchars($data['note'] ?? ''),
            $total_money,
            $payment_method, 
            $status,         
            htmlspecialchars($data['receiver_name'] ?? ''),
            htmlspecialchars($data['receiver_phone'] ?? ''),
            htmlspecialchars($data['receiver_address'] ?? '')
        ]);

        $order_id = $pdo->lastInsertId();

        // 2. Ghi chi tiết đơn hàng (order_details)
        $sqlDetail = "INSERT INTO order_details (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)";
        $stmtDetail = $pdo->prepare($sqlDetail);

        // Chuẩn bị lệnh trừ kho (Chỉ dùng luôn nếu là COD)
        $sqlUpdateStock = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmtUpdateStock = $pdo->prepare($sqlUpdateStock);

        foreach ($_SESSION['cart'] as $item) {
            $product_id = (int)$item['id'];
            $buy_qty = (int)$item['quantity'];

            $stmtDetail->execute([
                $order_id,
                $product_id,
                $item['price'],
                $buy_qty
            ]);

            // HÀNH ĐỘNG: Nếu là COD thì trừ kho ngay lập tức
            if ($payment_method === 'cod') {
                $stmtUpdateStock->execute([$buy_qty, $product_id, $buy_qty]);
                if ($stmtUpdateStock->rowCount() === 0) {
                    throw new Exception("Sản phẩm '" . $item['name'] . "' không đủ số lượng tồn kho!");
                }
            }
        }

        $pdo->commit();

        // Nếu là COD thì xóa giỏ hàng luôn, nếu là VNPAY thì giữ lại hoặc xóa tùy luồng (ở đây xóa luôn cho sạch)
        unset($_SESSION['cart']);

        echo json_encode(['status' => 'success', 'order_id' => $order_id, 'payment_method' => $payment_method]);
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}