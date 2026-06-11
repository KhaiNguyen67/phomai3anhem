<?php
require_once __DIR__ . '/../includes/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../includes/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOrderNotification($customerEmail, $customerName, $orderId, $status) {
    $mail = new PHPMailer(true);

    try {
        
        // --- CẤU HÌNH SERVER SMTP ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khaicc67@gmail.com'; // Email dùng để gửi đi
        $mail->Password   = 'nxfq xhll heys yorx';      // Mật khẩu ứng dụng Google (16 ký tự)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // --- NGƯỜI GỬI & NGƯỜI NHẬN ---
        $mail->setFrom('email_cua_ban@gmail.com', 'Phô Mai 3 Anh Em');
        $mail->addAddress($customerEmail, $customerName);

        // --- ĐỊNH DẠNG TRẠNG THÁI TIẾNG VIỆT ---
        $statusText = '';
        switch ($status) {
            case 'pending': $statusText = 'Chờ xử lý'; break;
            case 'confirmed': $statusText = 'Đã xác nhận đơn hàng'; break;
            case 'shipping': $statusText = 'Đang giao hàng'; break;
            case 'completed': $statusText = 'Giao hàng thành công (Hoàn thành)'; break;
            case 'cancelled': $statusText = 'Đã bị hủy'; break;
            default: $statusText = $status; break;
        }

        // --- NỘI DUNG EMAIL ---
        $mail->isHTML(true);
        $mail->Subject = "[Phô Mai 3 Anh Em] Cập nhật trạng thái đơn hàng #$orderId";
        
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px;'>
                <h2 style='color: #d97706;'>Cập Nhật Trạng Thái Đơn Hàng!</h2>
                <p>Xin chào <strong>$customerName</strong>,</p>
                <p>Cửa hàng <strong>Phô Mai 3 Anh Em</strong> xin thông báo đơn hàng của bạn đã được cập nhật trạng thái mới.</p>
                <hr style='border: none; border-top: 1px solid #eee;'>
                <p><strong>Mã đơn hàng:</strong> #$orderId</p>
                <p><strong>Trạng thái hiện tại:</strong> <span style='color: #2563eb; font-weight: bold;'>$statusText</span></p>
                <hr style='border: none; border-top: 1px solid #eee;'>
                <p>Cảm ơn bạn đã tin tưởng và đồng hành cùng chúng tôi.</p>
                <p style='font-size: 12px; color: #777;'>Đây là email tự động, vui lòng không phản hồi lại email này.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Bạn có thể ghi log lỗi ở đây nếu cần: $mail->ErrorInfo
        return false;
    }
}