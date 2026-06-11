<?php
// ============================================================
// File: thankyou.php
// Chức năng: Hiển thị lời cảm ơn và mã đơn hàng vừa mua
// ============================================================
include_once 'includes/header.php';
$order_id = $_GET['order_id'] ?? 0;
?>

<div class="container my-5 py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="mb-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 70px;"></i>
            </div>
            <h2 class="fw-bold text-dark mb-3">Đặt Hàng Thành Công!</h2>
            <p class="text-secondary lead mb-4">Cảm ơn bạn đã lựa chọn Phô Mai 3 Anh Em. Mã đơn hàng của bạn là: </p>
            <div class="bg-light p-4 rounded-4 mb-4 text-start small text-muted" style="line-height: 1.7;">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle-fill text-warning me-2"></i>Thông tin lưu ý:</h6>
                <li>Nhân viên cửa hàng sẽ liên hệ lại qua số điện thoại của bạn để xác nhận lộ trình giao hàng.</li>
                <li>Thời gian giao hàng dự kiến từ 30 - 60 phút trong khu vực nội thành TP. Hồ Chí Minh.</li>
                <li>Bạn vui lòng giữ điện thoại luôn trong trạng thái liên lạc được để Shipper gửi hàng nhé.</li>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 small fw-bold">Tiếp tục mua sắm</a>
                <a href="order-history.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 small fw-bold red">Xem Lịch Sử Đơn</a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>