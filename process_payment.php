<?php
// ============================================================
// File: process_payment.php
// Chức năng: Khởi tạo URL giao dịch VNPAY kết nối Database an toàn
// (Đã dọn dẹp phần up ảnh cũ vì đã có file process-proof.php xử lý)
// ============================================================
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
include_once 'config/db.php'; // Nhúng kết nối PDO $pdo từ hệ thống của bạn

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: checkout.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Lấy số tiền trực tiếp từ DB để đảm bảo tính an toàn bảo mật, tránh việc sửa giá tiền ở Client
$stmt = $pdo->prepare("SELECT total_money FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "<script>alert('Lỗi: Không tìm thấy đơn hàng trên hệ thống!'); window.location.href='checkout.php';</script>";
    exit();
}

$total_money = $order['total_money']; 

// ============================================================
// CẤU HÌNH THÔNG SỐ TRANG KIỂM THỬ VNPAY SANDBOX
// ============================================================
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html"; 
$vnp_TmnCode = "ADFQD7QS"; // Mã Website định danh cấu hình Test mặc định của VNPAY
$vnp_HashSecret = "YXDG7SS5VYVQGBRYA6IWKIGOY2NT8IQP"; // Chuỗi mã hóa bí mật tạo chữ ký dữ liệu

// Tự động định dạng liên kết dẫn quay lại trang kết quả trên host của bạn
$vnp_Returnurl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/vnpay_return.php";

$vnp_TxnRef = $order_id; 
$vnp_OrderInfo = "Thanh toan don hang phomai #" . $order_id;
$vnp_OrderType = "billpayment";
$vnp_Amount = $total_money * 100; // Nhân 100 theo đúng quy định định dạng số tiền của cổng VNPAY
$vnp_Locale = 'vn';
$vnp_BankCode = ''; // Để trống để khách thoải mái chọn phương thức thanh toán tại cổng VNPAY
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'), // Sẽ lấy chuẩn giờ VN sau khi đặt timezone ở trên
    "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes')), // <-- THÊM DÒNG NÀY (Hết hạn sau 15 phút)
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef
);

ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;
if (isset($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

// Chuyển hướng khách hàng tới cổng thanh toán VNPAY lập tức
header('Location: ' . $vnp_Url);
exit();
?>