<?php
// ============================================================
// File: admin-user-process.php
// Chức năng: Thực thi cập nhật và xóa thông tin users (PDO)
// ============================================================
include_once 'admin-check.php'; // Chặn tài khoản thường thao túng dữ liệu trái phép
include_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Thu thập và làm sạch dữ liệu
    $id = (int)$_POST['id'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);
    $address = trim($_POST['address']);

    if (!empty($id) && !empty($full_name) && !empty($role)) {
        // Cập nhật thông tin chi tiết của user (Không đổi Email để giữ định danh gốc)
        $sql = "UPDATE users SET 
                    full_name = :full_name, 
                    phone = :phone, 
                    role = :role, 
                    address = :address 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'full_name' => $full_name,
            'phone'     => $phone,
            'role'      => $role,
            'address'   => $address,
            'id'        => $id
        ]);

        // Nếu admin tự hạ quyền của chính mình xuống 'customer', cập nhật lại Session ngay lập tức
        if ($id == $_SESSION['user_id']) {
            $_SESSION['user_role'] = $role;
            // Nếu không còn là admin nữa thì đẩy văng ra ngoài trang chủ khách hàng luôn
            if ($role !== 'admin') {
                header("Location: ../index.php");
                exit();
            }
        }

        header("Location: admin-users.php?status=success_edit");
        exit();
    }

} elseif ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    // XỬ LÝ NGHIỆP VỤ XÓA THÀNH VIÊN
    if ($action === 'delete' && isset($_GET['id'])) {
        $delete_id = (int)$_GET['id'];

        // BẢO VỆ: Nếu id cần xóa trùng khớp với ID của admin đang đăng nhập hiện tại
        if ($delete_id == $_SESSION['user_id']) {
            header("Location: admin-users.php?status=error_self_delete");
            exit();
        }

        // Thực hiện xóa an toàn
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $delete_id]);

        header("Location: admin-users.php?status=success_delete");
        exit();
    }
}

// Chuyển hướng dự phòng nếu truy cập file sai mục đích
header("Location: admin-users.php");
exit();
?>