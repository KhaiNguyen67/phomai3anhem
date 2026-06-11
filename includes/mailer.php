<?php
// ============================================================
// File: includes/mailer.php
// ============================================================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Định hình đường dẫn tuyệt đối chuẩn xác bằng hằng số __DIR__
require_once __DIR__ . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';

function sendSystemMail($toEmail, $subject, $bodyContent) {
    $mail = new PHPMailer(true);

    try {
        // --- 1. Cấu hình thông số kết nối Máy chủ SMTP ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';                     // Máy chủ gửi mail của Google
        $mail->SMTPAuth   = true;                                 // Bật tính năng xác thực tài khoản
        $mail->Username   = 'khaicc67@gmail.com';            // TÀI KHOẢN GMAIL GỐC CỦA BẠN
        $mail->Password   = 'nxfq xhll heys yorx';                // MẬT KHẨU ỨNG DỤNG (16 ký tự tạo từ tài khoản Google)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Phương thức mã hóa an toàn mã luồng
        $mail->Port       = 587;                                  // Cổng cổng TLS chuẩn kết nối quốc tế
        $mail->CharSet    = 'UTF-8';                              // Khắc phục hoàn toàn lỗi bể phông chữ Tiếng Việt

        // --- 2. Cấu hình Thực thể nhận dạng thương hiệu ---
        $mail->setFrom('khaicc67@gmail.com', 'Phô Mai 3 Anh Em');
        $mail->addAddress($toEmail);                              // Địa chỉ Email người nhận

        // --- 3. Biên soạn hình thức và lõi nội dung bức thư ---
        $mail->isHTML(true);                                      // Chấp nhận cấu trúc dạng HTML thiết kế đồ họa
        $mail->Subject = $subject;
        $mail->Body    = $bodyContent;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Bạn có thể ghi đè log lỗi ra nếu cần debug bằng lệnh: error_log($mail->ErrorInfo);
        return false;
    }
}