<?php
// ============================================================
// File: contact-process.php
// Chức năng: Xử lý tiếp nhận form liên hệ từ khách hàng (PDO)
// ============================================================
include_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Thu thập và làm sạch dữ liệu đầu vào chống XSS
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? 'Liên hệ mới từ Website');
    $message = trim($_POST['message'] ?? '');

    // Kiểm tra dữ liệu bắt buộc không được để trống
    if (empty($fullname) || empty($email) || empty($phone) || empty($message)) {
        header("Location: contact.php?status=error");
        exit();
    }

    try {
        // Thực hiện thêm dữ liệu vào bảng contacts
        $sql = "INSERT INTO contacts (fullname, email, phone, subject, message) 
                VALUES (:fullname, :email, :phone, :subject, :message)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'fullname' => $fullname,
            'email'    => $email,
            'phone'    => $phone,
            'subject'  => $subject,
            'message'  => $message
        ]);

        // Thành công: Quay lại trang và báo tin thành công
        header("Location: contact.php?status=success");
        exit();

    } catch (PDOException $e) {
        // Lỗi hệ thống cơ sở dữ liệu
        header("Location: contact.php?status=error");
        exit();
    }
} else {
    // Chặn truy cập trái phép bằng phương thức GET
    header("Location: contact.php");
    exit();
}
?>