<?php
// ============================================================
// File: order-detail.php
// Chức năng: Xem chi tiết đơn hàng & Hiện cảnh báo nếu bị Hủy
// ============================================================
session_start();
include_once 'config/db.php';
include_once 'includes/header.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Lấy thông tin tổng quan đơn hàng
$stmt_order = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt_order->execute([$order_id]);
$order = $stmt_order->fetch();

if (!$order) {
    echo "<div class='container my-5 py-5'><div class='alert alert-danger rounded-4 text-center p-4 fw-bold shadow-sm'><i class='bi bi-exclamation-triangle-fill me-2'></i>Mã đơn hàng không hợp lệ hoặc không tồn tại trên hệ thống.</div></div>";
    include_once 'includes/footer.php';
    exit();
}

// 2. Lấy chi tiết sản phẩm trong đơn hàng
$stmt_items = $pdo->prepare("SELECT od.*, p.name AS product_name, p.image AS product_image 
                             FROM order_details od 
                             LEFT JOIN products p ON od.product_id = p.id 
                             WHERE od.order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();
?>

<div class="container my-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark m-0"><i class="bi bi-file-earmark-text text-warning me-2"></i>Đơn Hàng #<?= $order['id'] ?></h4>
                <a href="order-history.php" class="btn btn-outline-secondary btn-sm px-3 rounded-pill small"><i class="bi bi-arrow-left me-1"></i> Trở lại</a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <div class="row position-relative text-center g-0 timeline-container">
                    <?php 
                        $status = $order['status'];
                        $step1 = ''; $step2 = ''; $step3 = '';
                        
                        if ($status == 'pending' || $status == 'pending_proof') {
                            $step1 = 'active';
                        } elseif ($status == 'shipping') {
                            $step1 = 'active';
                            $step2 = 'active';
                        } elseif ($status == 'completed') {
                            $step1 = 'active';
                            $step2 = 'active';
                            $step3 = 'active';
                        }
                    ?>
                    
                    <?php if ($status == 'cancelled'): ?>
                        <div class="col-12 text-center py-2">
                            <div class="alert alert-danger border-0 rounded-3 m-0 py-3 shadow-sm">
                                <h5 class="fw-bold m-0 text-danger"><i class="bi bi-x-circle-fill me-2"></i> ĐƠN HÀNG NÀY ĐÃ BỊ HỦY BỎ</h5>
                                <p class="small text-muted m-0 mt-1">Đơn hàng của bạn đã bị hủy trên hệ thống quản lý bởi ban quản trị cửa hàng.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-4 timeline-step <?= $step1 ?>">
                            <div class="step-icon mx-auto bg-warning text-white">
                                <?php if ($status == 'pending_proof'): ?>
                                    <i class="bi bi-hourglass-split"></i>
                                <?php else: ?>
                                    <i class="bi bi-cart-check-fill"></i>
                                <?php endif; ?>
                            </div>
                            <div class="fw-bold small mt-2 text-dark">
                                <?= $status == 'pending_proof' ? 'Chờ duyệt ảnh' : 'Đã tiếp nhận' ?>
                            </div>
                            <small class="text-muted d-block" style="font-size:11px;"><?= date('d/m/Y', strtotime($order['created_at'])) ?></small>
                        </div>
                        <div class="col-4 timeline-step <?= $step2 ?>">
                            <div class="step-icon mx-auto"><i class="bi bi-truck"></i></div>
                            <div class="fw-bold small mt-2 text-dark">Đang vận chuyển</div>
                        </div>
                        <div class="col-4 timeline-step <?= $step3 ?>">
                            <div class="step-icon mx-auto"><i class="bi bi-house-heart-fill"></i></div>
                            <div class="fw-bold small mt-2 text-dark">Giao thành công</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-warning"><i class="bi bi-geo-alt-fill me-2"></i>Thông Tin Giao Hàng</h5>
                        
                        <div class="mb-3 small">
                            <strong class="text-secondary d-block">Người nhận hàng:</strong>
                            <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($order['receiver_name'] ?: $order['full_name']) ?></span>
                        </div>
                        <div class="mb-3 small">
                            <strong class="text-secondary d-block">Số điện thoại:</strong>
                            <span class="text-dark fw-semibold"><?= htmlspecialchars($order['receiver_phone'] ?: $order['phone']) ?></span>
                        </div>
                        <div class="mb-3 small">
                            <strong class="text-secondary d-block">Địa chỉ nhận phô mai:</strong>
                            <span class="text-dark"><?= htmlspecialchars($order['receiver_address'] ?: $order['address']) ?></span>
                        </div>
                        <div class="mb-0 small">
                            <strong class="text-secondary d-block">Ghi chú yêu cầu:</strong>
                            <span class="text-muted italic">"<?= htmlspecialchars($order['note'] ?: 'Không có ghi chú nào.') ?>"</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-warning"><i class="bi bi-box-seam-fill me-2"></i>Sản Phẩm Đã Đặt</h5>
                        
                        <div class="order-items-list mb-3">
                            <?php foreach ($items as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                            ?>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="border rounded-3 p-1 bg-light" style="width: 50px; height: 50px;">
                                        <img src="assets/img/<?= htmlspecialchars($item['product_image'] ?: 'default-cheese.png') ?>" class="w-100 h-100" style="object-fit: contain;">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 small"><?= htmlspecialchars($item['product_name'] ?? 'Sản phẩm đã bị xóa') ?></h6>
                                        <small class="text-muted"><?= number_format($item['price'], 0, ',', '.') ?>đ x <?= $item['quantity'] ?></small>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark small"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="bg-light rounded-3 p-3 mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Tổng số tiền thanh toán:</span>
                                <span class="fw-bold text-danger fs-5"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.timeline-container::before {
    content: "";
    position: absolute;
    top: 20px; left: 15%; right: 15%;
    height: 4px; background-color: #e0e0e0;
    z-index: 1;
}
.timeline-step { position: relative; z-index: 2; }
.step-icon {
    width: 44px; height: 44px; border-radius: 50%;
    background-color: #e0e0e0; color: #a0a0a0;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; border: 3px solid #fff; transition: all 0.3s;
}
.timeline-step.active .step-icon { background-color: #198754 !important; color: white !important; box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.15); }
.timeline-step.active .fw-bold { color: #198754 !important; }
.italic { font-style: italic; }
</style>

<?php include_once 'includes/footer.php'; ?>