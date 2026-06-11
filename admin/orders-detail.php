<?php 
// ============================================================
// File: admin/orders-detail.php
// Chức năng: Xem chi tiết đơn hàng (Tích hợp hiển thị Minh chứng chuyển khoản từ khách)
// ============================================================
include_once 'admin-check.php';
include_once '../config/db.php'; 
include_once 'includes/header.php'; 

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Truy vấn thông tin tổng quan của đơn hàng (Bảng orders bao gồm các trường mới)
$stmt_order = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt_order->execute([$order_id]);
$order = $stmt_order->fetch();

if (!$order) {
    echo "<div class='container mt-5'><div class='alert alert-danger rounded-3 small'>Mã đơn hàng không hợp lệ hoặc đã bị xóa khỏi hệ thống.</div></div>";
    include_once 'includes/footer.php';
    exit();
}

// 2. Truy vấn chi tiết sản phẩm thuộc đơn (JOIN bảng order_details với bảng products để lấy tên sản phẩm)
$sql_items = "SELECT od.*, p.name AS product_name 
              FROM order_details od 
              LEFT JOIN products p ON od.product_id = p.id 
              WHERE od.order_id = ?";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0 text-dark"><i class="bi bi-file-earmark-text me-2"></i>Chi Tiết Đơn Hàng #<?php echo $order['id']; ?></h4>
        <a href="orders.php" class="btn btn-outline-secondary btn-sm px-3 rounded-pill small"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
    </div>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card admin-card bg-white border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-info">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-info"><i class="bi bi-person-badge-fill me-2"></i>Thông Tin Người Mua</h5>
                <div class="mb-2 small"><strong>Họ và tên:</strong> <?php echo htmlspecialchars($order['full_name']); ?></div>
                <div class="mb-2 small"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></div>
                <div class="mb-0 small"><strong>Địa chỉ Email:</strong> <?php echo htmlspecialchars($order['email'] ?: 'Không cung cấp'); ?></div>
            </div>

            <div class="card admin-card bg-white border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-warning">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-warning"><i class="bi bi-truck me-2"></i>Thông Tin Người Nhận</h5>
                <div class="mb-2 small"><strong>Họ và tên:</strong> <?php echo htmlspecialchars($order['receiver_name'] ?: $order['full_name']); ?></div>
                <div class="mb-2 small"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['receiver_phone'] ?: $order['phone']); ?></div>
                <div class="mb-2 small"><strong>Địa chỉ giao:</strong> <span class="text-dark fw-semibold"><?php echo htmlspecialchars($order['receiver_address'] ?: $order['address']); ?></span></div>
                <div class="mb-0 small"><strong>Ghi chú từ khách:</strong> <span class="text-muted"><?php echo htmlspecialchars($order['note'] ?: 'Không có ghi chú.'); ?></span></div>
            </div>

            <div class="card admin-card bg-white border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-purple" style="border-left-color: #6f42c1 !important;">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-purple" style="color: #6f42c1;"><i class="bi bi-credit-card-2-front-fill me-2"></i>Bằng Chứng Thanh Toán</h5>
                <?php if (!empty($order['payment_proof']) && file_exists('../' . $order['payment_proof'])): ?>
                    <p class="small text-muted mb-2">Khách hàng chọn hình thức chuyển khoản và đã tải lên ảnh biên lai bên dưới:</p>
                    <div class="text-center p-2 bg-light border rounded-3">
                        <a href="../<?php echo htmlspecialchars($order['payment_proof']); ?>" target="_blank" title="Click để phóng to ảnh biên lai">
                            <img src="../<?php echo htmlspecialchars($order['payment_proof']); ?>" alt="Biên lai chuyển khoản đơn hàng" class="img-fluid rounded-2 shadow-sm" style="max-height: 280px; object-fit: contain;">
                        </a>
                        <div class="mt-2 text-secondary" style="font-size: 11px;">
                            <i class="bi bi-zoom-in me-1"></i> Nhấp vào ảnh để phóng to ở tab mới
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary m-0 p-3 rounded-3 small text-center text-muted">
                        <i class="bi bi-info-circle me-1"></i> Đơn hàng này không sử dụng hình thức chuyển khoản trực tuyến hoặc khách hàng chưa gửi ảnh minh chứng biên lai.
                    </div>
                <?php endif; ?>
            </div>

            <div class="card admin-card bg-white border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="bi bi-shield-check me-2"></i>Trạng Thái Xử Lý</h5>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="small">Tình trạng đơn hàng:</span>
                    <span class="badge py-2 px-3 rounded-pill fs-6 <?php 
                        if ($order['status'] == 'pending') echo 'bg-warning text-dark';
                        elseif ($order['status'] == 'pending_proof') echo 'bg-purple text-white';
                        elseif ($order['status'] == 'shipping') echo 'bg-info text-white';
                        elseif ($order['status'] == 'completed') echo 'bg-success text-white';
                        else echo 'bg-danger text-white';
                    ?>">
                        <?php 
                            if($order['status'] == 'pending') echo "CHỜ XỬ LÝ (COD)";
                            elseif($order['status'] == 'pending_proof') echo "CHỜ DUYỆT ẢNH (BANK)";
                            elseif($order['status'] == 'shipping') echo "ĐANG GIAO HÀNG";
                            elseif($order['status'] == 'completed') echo "THÀNH CÔNG";
                            else echo "ĐÃ HỦY ĐƠN";
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card admin-card bg-white border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-warning"><i class="bi bi-box-seam me-2"></i>Giỏ Hàng Đặt Mua</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center m-0 small">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th class="text-start">Tên mặt hàng sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>SL</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach($items as $item): 
                                // Tự động tính toán thành tiền của từng dòng dựa trên giá và số lượng
                                $subtotal = $item['price'] * $item['quantity'];
                            ?>
                            <tr>
                                <td class="text-start fw-bold text-dark"><?php echo htmlspecialchars($item['product_name'] ?? 'Sản phẩm đã bị xóa khỏi hệ thống'); ?></td>
                                <td><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td class="text-danger fw-bold"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="table-light fs-6">
                                <td colspan="3" class="text-end fw-bold text-dark">Tổng tiền phải thu:</td>
                                <td class="text-danger fw-bold fs-5"><?php echo number_format($order['total_money'], 0, ',', '.'); ?>đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple { background-color: #6f42c1 !important; }
.text-purple { color: #6f42c1 !important; }
.admin-card { transition: all 0.25s ease-in-out; }
.admin-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }
</style>

<?php include_once 'includes/footer.php'; ?>