<?php
// ============================================================
// File: process_proof.php
// Chức năng: Xử lý upload ảnh minh chứng chuyển khoản từ Client
// ============================================================
session_start();
include_once 'config/db.php'; 

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Lấy order_id từ Form gửi lên
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    if ($order_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Mã đơn hàng không hợp lệ.']);
        exit();
    }

    // 2. Kiểm tra xem file có lỗi từ phía PHP cấu hình không (như quá dung lượng...)
    if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
        $error_code = $_FILES['proof_image']['error'] ?? 'Không tìm thấy file';
        echo json_encode(['status' => 'error', 'message' => 'Lỗi tải file lên Server (Mã lỗi: ' . $error_code . ').']);
        exit();
    }

    $file = $_FILES['proof_image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

    // 3. Kiểm tra định dạng đuôi file ảnh hợp lệ
    if (!in_array($ext, $allowed_extensions)) {
        echo json_encode(['status' => 'error', 'message' => 'Định dạng ảnh không hỗ trợ (Chỉ nhận JPG, PNG, WEBP).']);
        exit();
    }

    // 4. Cấu hình thư mục lưu trữ theo assets/img/proofs/
    $target_dir = "assets/img/proofs/";
    if (!is_dir($target_dir)) {
        // Tự động tạo thư mục proofs với toàn quyền nếu chưa tồn tại trên localhost
        mkdir($target_dir, 0777, true); 
    }

    // Tạo tên file ngẫu nhiên theo mã đơn và thời gian để tránh trùng tên file ảnh
    $new_file_name = "qr_" . $order_id . "_" . time() . "." . $ext;
    $target_file = $target_dir . $new_file_name;

    // 5. Di chuyển file từ thư mục tạm vào thư mục assets/img/proofs/
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        try {
            // Cập nhật trạng thái đơn hàng thành 'pending_proof' và ghi nhận đường dẫn ảnh vào CSDL
            $stmt = $pdo->prepare("UPDATE orders SET status = 'pending_proof', payment_proof = ? WHERE id = ?");
            $result = $stmt->execute([$target_file, $order_id]);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Tải ảnh minh chứng thành công! Đang chờ Admin duyệt.']);
                exit();
            } else {
                if (file_exists($target_file)) { unlink($target_file); } // Xóa ảnh nếu update CSDL lỗi
                echo json_encode(['status' => 'error', 'message' => 'Không thể cập nhật trạng thái đơn hàng vào CSDL.']);
                exit();
            }
        } catch (PDOException $e) {
            if (file_exists($target_file)) { unlink($target_file); }
            echo json_encode(['status' => 'error', 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể di chuyển tệp tin vào thư mục assets/img/proofs/.']);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.']);
    exit();
}