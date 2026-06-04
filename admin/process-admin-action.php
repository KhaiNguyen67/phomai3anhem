<?php
// ============================================================
// File: admin/process_admin_action.php
// Chức năng: API xử lý thao tác Duyệt/Hủy/Đổi trạng thái đơn hàng từ Admin qua Fetch API
// Đường dẫn: phomai3anhem/admin/process_admin_action.php
// ============================================================
session_start();
include_once 'admin-check.php'; // Đảm bảo chỉ Admin mới có quyền truy cập file này
include_once '../config/db.php'; 

// Thiết lập định dạng dữ liệu đầu ra là JSON
header('Content-Type: application/json; charset=utf-8');

// Chỉ chấp nhận phương thức POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Phương thức yêu cầu không hợp lệ.'
    ]);
    exit();
}

/**
 * Nhận và chuẩn hóa dữ liệu đầu vào từ Client gửi lên.
 * Hỗ trợ cả 2 kiểu gửi: FormData (x-www-form-urlencoded) và JSON Raw (application/json)
 */
$order_id = 0;
$status = '';

// Trường hợp 1: Nhận từ FormData (Khớp với đối tượng new FormData() trong JS)
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = trim($_POST['status']);
} else {
    // Trường hợp 2: Dự phòng nhận từ luồng JSON body ngầm (nếu có thay đổi kiểu gửi sau này)
    $inputRaw = file_get_contents('php://input');
    $inputData = json_decode($inputRaw, true);
    if (!empty($inputData['order_id']) && !empty($inputData['status'])) {
        $order_id = (int)$inputData['order_id'];
        $status = trim($inputData['status']);
    }
}

// Kiểm tra tính hợp lệ của dữ liệu đầu vào
if ($order_id <= 0 || empty($status)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dữ liệu truyền lên không đầy đủ hoặc không hợp lệ.'
    ]);
    exit();
}

// Danh sách các trạng thái đơn hàng được phép cập nhật trong CSDL
$allowed_statuses = ['pending', 'pending_proof', 'shipping', 'completed', 'cancelled'];

if (!in_array($status, $allowed_statuses)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Trạng thái đơn hàng không hợp lệ.'
    ]);
    exit();
}

try {
    // 1. Kiểm tra xem đơn hàng có tồn tại thực tế trong hệ thống hay không
    $checkStmt = $pdo->prepare("SELECT id, status FROM orders WHERE id = ?");
    $checkStmt->execute([$order_id]);
    $order = $checkStmt->fetch();

    if (!$order) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Đơn hàng #' . $order_id . ' không tồn tại trên hệ thống.'
        ]);
        exit();
    }

    // 2. Tiến hành cập nhật trạng thái mới vào Cơ sở dữ liệu
    $updateStmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $result = $updateStmt->execute([$status, $order_id]);

    if ($result) {
        // Trả về phản hồi thành công cho Fetch API xử lý giao diện (nháy màu, đổi huy hiệu)
        echo json_encode([
            'status' => 'success',
            'message' => 'Cập nhật trạng thái đơn hàng #' . $order_id . ' thành công.'
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Thao tác cập nhật thất bại, vui lòng thử lại.'
        ]);
        exit();
    }

} catch (PDOException $e) {
    // Ghi lại log lỗi hệ thống nếu cần và trả về thông báo an toàn
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi kết nối CSDL hệ thống: ' . $e->getMessage()
    ]);
    exit();
}