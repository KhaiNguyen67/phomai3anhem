<?php
// ============================================================
// File: vnpay_return.php
// Chức năng: Đọc dữ liệu trả về từ VNPAY, kiểm tra mã băm bảo mật,
//            cập nhật trạng thái đơn hàng, TỰ ĐỘNG TRỪ KHO và GỬI MAIL khi thành công.
// ============================================================
session_start();
include_once 'config/db.php'; // Nhúng kết nối PDO $pdo
include_once 'includes/header.php'; // Nhúng giao diện header để hiển thị thông báo đẹp mắt

// ── GỌI THƯ VIỆN PHPMAILER ─────────────────────────────────
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/includes/PHPMailer/src/Exception.php';
require_once __DIR__ . '/includes/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/includes/PHPMailer/src/SMTP.php';

// ── HÀM XỬ LÝ GỬI EMAIL HÓA ĐƠN TỰ ĐỘNG ─────────────────────
function sendOrderConfirmationEmail($customer_email, $customer_name, $order_id, $total_price, $cart_items) {
    $mail = new PHPMailer(true);
    try {
        // Cấu hình Server SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khaicc67@gmail.com'; 
        $mail->Password   = 'nxfq xhll heys yorx'; // Mật khẩu ứng dụng (App Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Người gửi & Người nhận
        $mail->setFrom('khaicc67@gmail.com', 'Phô Mai 3 Anh Em');
        $mail->addAddress($customer_email, $customer_name);

        // Tạo danh sách sản phẩm định dạng HTML bảng (Table Rows)
        $items_html = '';
        foreach ($cart_items as $item) {
            $product_name = htmlspecialchars($item['product_name'] ?? $item['name'] ?? 'Sản phẩm phô mai');
            $quantity     = intval($item['quantity']);
            $price        = number_format($item['price'], 0, ',', '.') . 'đ';
            $subtotal     = number_format($item['price'] * $item['quantity'], 0, ',', '.') . 'đ';
            
            $items_html .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #e2e8f0;'>$product_name</td>
                    <td style='padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: center;'>$quantity</td>
                    <td style='padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;'>$price</td>
                    <td style='padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #d97706; font-weight: bold;'>$subtotal</td>
                </tr>
            ";
        }

        // Nội dung Mail định dạng HTML template chuyên nghiệp
        $mail->isHTML(true);
        $mail->Subject = "=?UTF-8?B?" . base64_encode("Xác nhận đơn hàng trực tuyến thành công #" . $order_id) . "?=";
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px; background-color: #ffffff;'>
                <div style='text-align: center; border-bottom: 2px solid #f59e0b; padding-bottom: 15px; margin-bottom: 20px;'>
                    <h2 style='color: #d97706; margin: 0;'>Phô Mai 3 Anh Em</h2>
                    <p style='color: #64748b; margin: 5px 0 0 0;'>Cảm ơn bạn đã đặt hàng tại cửa hàng chúng tôi!</p>
                </div>
                
                <h3 style='color: #1e293b;'>Xin chào $customer_name,</h3>
                <p style='color: #334155; line-height: 1.5;'>Đơn hàng của bạn đã được thanh toán qua cổng <strong>VNPAY</strong> và ghi nhận thành công.</p>
                
                <div style='background-color: #f8fafc; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;'>
                    <strong>Mã đơn hàng:</strong> #$order_id <br>
                    <strong>Thời gian đặt hàng:</strong> " . date('d/m/Y H:i') . " <br>
                    <strong>Phương thức thanh toán:</strong> Thẻ ATM / QR-Code VNPAY <br>
                    <strong>Trạng thái giao dịch:</strong> <span style='color: #16a34a; font-weight: bold;'>Thành công</span>
                </div>

                <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 20px;'>
                    <thead>
                        <tr style='background-color: #f1f5f9; text-align: left;'>
                            <th style='padding: 10px; border-bottom: 2px solid #cbd5e1;'>Sản phẩm</th>
                            <th style='padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: center;'>SL</th>
                            <th style='padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: right;'>Đơn giá</th>
                            <th style='padding: 10px; border-bottom: 2px solid #cbd5e1; text-align: right;'>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        $items_html
                    </tbody>
                </table>

                <div style='text-align: right; font-size: 16px; margin-bottom: 30px;'>
                    <strong>Tổng tiền đã thanh toán:</strong> 
                    <span style='color: #dc2626; font-size: 20px; font-weight: bold; margin-left: 10px;'>" . number_format($total_price, 0, ',', '.') . "đ</span>
                </div>

                <p style='font-size: 14px; color: #475569; line-height: 1.5;'>Hệ thống cửa hàng đang chuẩn bị đóng gói sản phẩm và sẽ giao tới địa chỉ của bạn trong thời gian sớm nhất.</p>
                
                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                <p style='font-size: 12px; color: #94a3b8; text-align: center;'>Đây là email gửi tự động từ Website Cửa hàng Phô Mai 3 Anh Em. Vui lòng không trả lời trực tiếp email này.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Lỗi gửi mail hệ thống VNPAY: " . $mail->ErrorInfo);
        return false;
    }
}

// ── XỬ LÝ CHECK CHỮ KÝ VÀ TRẠNG THÁI TỪ VNPAY ─────────────────
$vnp_HashSecret = "YXDG7SS5VYVQGBRYA6IWKIGOY2NT8IQP"; 

$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

$secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

$payment_status = 'error'; 
$message = 'Lỗi bảo mật nghiêm trọng: Chữ ký băm bảo mật không hợp lệ!';

if ($secureHash === $vnp_SecureHash) {
    $order_id = intval($_GET['vnp_TxnRef']);
    $vnp_ResponseCode = $_GET['vnp_ResponseCode']; 
    
    if ($vnp_ResponseCode == '00') {
        try {
            $pdo->beginTransaction();

            // 1. Kiểm tra trạng thái đơn hàng hiện tại trong CSDL để tránh xử lý lặp khi khách F5 trang
            $stmtOrder = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $stmtOrder->execute([$order_id]);
            $order = $stmtOrder->fetch();

            if ($order && $order['status'] === 'unpaid') {
                // 2. Cập nhật trạng thái đơn hàng thành "shipping" (Đang giao) và lưu phương thức vnpay
                $stmtUpdateOrder = $pdo->prepare("UPDATE orders SET status = 'shipping', payment_method = 'vnpay' WHERE id = ?");
                $stmtUpdateOrder->execute([$order_id]);

                // 3. Lấy chi tiết các sản phẩm thuộc đơn hàng này để chuẩn bị trừ kho và đưa vào email
                // LƯU Ý: JOIN thêm bảng products nếu bảng order_details của bạn không lưu sẵn cột tên sản phẩm (name)
                $stmtItems = $pdo->prepare("
                    SELECT od.*, p.name AS product_name 
                    FROM order_details od
                    JOIN products p ON od.product_id = p.id
                    WHERE od.order_id = ?
                ");
                $stmtItems->execute([$order_id]);
                $items = $stmtItems->fetchAll();

                // Câu lệnh cập nhật giảm kho an toàn (Chỉ trừ khi số lượng kho đủ đáp ứng)
                $sqlUpdateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                
                foreach ($items as $item) {
                    $sqlUpdateStock->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
                    
                    if ($sqlUpdateStock->rowCount() === 0) {
                        throw new Exception("Thanh toán thành công nhưng sản phẩm [" . $item['product_name'] . "] trong kho vừa hết hàng!");
                    }
                }
                
                // 4. THỰC THI GỬI EMAIL THÔNG BÁO CHO KHÁCH HÀNG
                $customer_email = $order['email'];
                $customer_name  = $order['fullname'] ?? $order['name'] ?? 'Khách hàng';
                $total_price    = $order['total_price'] ?? ($order['total'] ?? ($_GET['vnp_Amount'] / 100));

                sendOrderConfirmationEmail($customer_email, $customer_name, $order_id, $total_price, $items);
                
                $pdo->commit();
                
                // Làm sạch giỏ hàng hiện tại sau khi mọi công đoạn thành công hoàn toàn
                unset($_SESSION['cart']);
                $payment_status = 'success';
                $message = 'Thanh toán đơn hàng thành công qua cổng VNPAY! Hóa đơn điện tử đã được gửi tới Email của bạn.';
            } else {
                // Đơn hàng đã được xử lý thành công trước đó (F5 hoặc IPN chạy trước)
                $pdo->rollBack();
                $payment_status = 'success';
                $message = 'Đơn hàng này đã được hệ thống ghi nhận thanh toán và gửi email xác nhận trước đó.';
            }

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $payment_status = 'warning';
            $message = 'Sự cố đồng bộ kho hàng: ' . $e->getMessage();
        }
    } else {
        $payment_status = 'cancel';
        $message = 'Giao dịch không thành công hoặc bạn đã hủy thao tác trên hệ thống VNPAY.';
    }
}
?>

<div class="container my-5 text-center">
    <div class="card border-0 shadow-sm rounded-4 p-5 mx-auto bg-white" style="max-width: 600px;" data-aos="zoom-in">
        <?php if ($payment_status === 'success'): ?>
            <i class="bi bi-check-circle-fill text-success display-1 animate-pulse" style="font-size: 5rem;"></i>
            <h3 class="fw-bold mt-4 text-success">Thanh Toán Thành Công!</h3>
            <p class="text-muted my-3"><?php echo $message; ?></p>
            <div class="alert alert-success border-0 rounded-3 small">
                Mã đơn hàng của bạn: <strong>#<?php echo isset($order_id) ? $order_id : 'N/A'; ?></strong>
            </div>
            
        <?php elseif ($payment_status === 'warning'): ?>
            <i class="bi bi-exclamation-triangle-fill text-warning display-1" style="font-size: 5rem;"></i>
            <h3 class="fw-bold mt-4 text-warning">Sự Cố Hệ Thống</h3>
            <p class="text-danger my-3"><?php echo $message; ?></p>
            <p class="text-muted small">Vui lòng chụp màn hình trang này và liên hệ tổng đài để được hỗ trợ kiểm tra thủ công.</p>

        <?php else: ?>
            <i class="bi bi-x-circle-fill text-danger display-1" style="font-size: 5rem;"></i>
            <h3 class="fw-bold mt-4 text-danger">Giao Dịch Thất Bại</h3>
            <p class="text-muted my-3"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <div class="mt-4 gap-3 d-flex justify-content-center">
            <a href="product.php" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2">
                <i class="bi bi-shop me-1"></i> Tiếp tục mua sắm
            </a>
            <a href="order-history.php" class="btn btn-gold btn-sm rounded-pill px-4 py-2 text-dark fw-bold shadow-sm">
                <i class="bi bi-clock-history me-1"></i> Lịch sử đơn hàng
            </a>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
.animate-pulse {
    animation: pulse 2s infinite ease-in-out;
}
</style>

<?php 
include_once 'includes/footer.php'; 
?>