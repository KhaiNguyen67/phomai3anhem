<?php
// ============================================================
// File: process-proof.php
// Chức năng: Xử lý upload ảnh minh chứng chuyển khoản từ Client
// ============================================================
session_start();
include_once 'config/db.php'; 

header('Content-Type: application/json; charset=utf-8');

// Chỉ chấp nhận phương thức POST gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Lấy order_id từ Form gửi lên và ép kiểu số nguyên
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    if ($order_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Mã đơn hàng không hợp lệ.']);
        exit();
    }

    // 2. Kiểm tra xem file có lỗi từ phía PHP cấu hình không (quá dung lượng upload của server...)
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
        echo json_encode(['status' => 'error', 'message' => 'Định dạng ảnh không hỗ trợ (Chỉ nhận JPG, JPEG, PNG, WEBP).']);
        exit();
    }

    // Kiểm tra dung lượng file từ phía Server (Giới hạn tối đa dưới 5MB để bảo vệ dung lượng host)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Dung lượng ảnh quá lớn. Vui lòng chọn ảnh dưới 5MB.']);
        exit();
    }

    // 4. Cấu hình thư mục lưu trữ theo assets/img/proofs/
    $target_dir = "assets/img/proofs/";
    if (!is_dir($target_dir)) {
        // Tự động tạo thư mục proofs nếu chưa tồn tại trên localhost
        mkdir($target_dir, 0777, true); 
    }

    // Tạo tên file ngẫu nhiên theo mã đơn và thời gian để tránh trùng tên đè file ảnh cũ
    $new_file_name = "qr_" . $order_id . "_" . time() . "." . $ext;
    $target_file = $target_dir . $new_file_name;

    // 5. Di chuyển file từ thư mục tạm vào thư mục assets/img/proofs/ chính thức
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        try {
            // Bảo mật: Kiểm tra xem đơn hàng này có đúng của user đang đăng nhập không (nếu hệ thống bắt buộc đăng nhập)
            if (isset($_SESSION['user_id'])) {
                $stmt_check = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
                $stmt_check->execute([$order_id, $_SESSION['user_id']]);
                if (!$stmt_check->fetch()) {
                    if (file_exists($target_file)) { unlink($target_file); }
                    echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền gửi minh chứng cho đơn hàng này.']);
                    exit();
                }
            }

            // Cập nhật trạng thái đơn hàng thành 'pending_proof' và ghi nhận đường dẫn vào trường payment_proof
            $stmt = $pdo->prepare("UPDATE orders SET status = 'pending_proof', payment_proof = ? WHERE id = ?");
            $result = $stmt->execute([$target_file, $order_id]);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Tải ảnh minh chứng thành công! Đang chờ Admin duyệt.']);
                exit();
            } else {
                if (file_exists($target_file)) { unlink($target_file); } // Xóa file thực tế ngoài thư mục nếu cập nhật CSDL thất bại
                echo json_encode(['status' => 'error', 'message' => 'Không thể cập nhật trạng thái đơn hàng vào CSDL.']);
                exit();
            }
        } catch (PDOException $e) {
            if (file_exists($target_file)) { unlink($target_file); } // Xóa file thực tế khi dính ngoại lệ CSDL
            echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối CSDL: ' . $e->getMessage()]);
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