<?php
// ============================================================
// File: order-history.php
// Chức năng: Xem danh sách lịch sử đơn hàng của khách hàng
// ============================================================
session_start();
include_once 'config/db.php';
include_once 'includes/header.php';

// Kiểm tra xem khách hàng đã đăng nhập chưa
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$orders = [];
$search_phone = isset($_GET['search_phone']) ? trim($_GET['search_phone']) : '';

if ($user_id > 0) {
    // Lấy đơn hàng theo user_id
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
} elseif (!empty($search_phone)) {
    // Nếu chưa đăng nhập nhưng có tra cứu bằng số điện thoại
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE phone = ? OR receiver_phone = ? ORDER BY id DESC");
    $stmt->execute([$search_phone, $search_phone]);
    $orders = $stmt->fetchAll();
}
?>

<div class="container my-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-clock-history fs-3 text-warning"></i>
                <h3 class="fw-bold text-dark m-0">Lịch Sử Mua Hàng</h3>
            </div>

            <?php if ($user_id == 0): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-2"><i class="bi bi-search me-2 text-warning"></i>Tra cứu đơn hàng nhanh</h5>
                    <p class="text-muted small">Bạn chưa đăng nhập tài khoản. Vui lòng nhập số điện thoại đặt hàng để kiểm tra trạng thái đơn.</p>
                    <form method="GET" action="order-history.php" class="row g-2">
                        <div class="col-md-8">
                            <input type="tel" name="search_phone" class="form-control rounded-3 py-2" 
                                   value="<?= htmlspecialchars($search_phone) ?>" placeholder="Nhập số điện thoại đã dùng đặt hàng..." required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-gold text-white w-100 py-2 rounded-pill fw-bold">Tìm kiếm đơn</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <?php if (empty($orders)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bag-x display-4 d-block mb-3 text-secondary"></i>
                        <?php if ($user_id == 0 && empty($search_phone)): ?>
                            <p class="m-0">Vui lòng nhập số điện thoại bên trên để tìm kiếm lịch sử đơn hàng của bạn.</p>
                        <?php else: ?>
                            <p class="m-0">Không tìm thấy đơn hàng nào thuộc về bạn hoặc đơn hàng đã bị xóa khỏi hệ thống quản lý.</p>
                        <?php endif; ?>
                        <a href="index.php" class="btn btn-outline-warning btn-sm rounded-pill px-4 mt-3 fw-bold">Tiếp tục mua sắm</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="table-light text-secondary small text-center">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th class="text-start">Người nhận & Địa chỉ</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $row): 
                                    // Thiết lập màu badge trạng thái đồng bộ hệ thống quản lý
                                    $status_badge = 'bg-secondary-subtle text-secondary';
                                    $status_text = 'Chờ xử lý';
                                    
                                    if ($row['status'] == 'pending_proof') { 
                                        $status_badge = 'bg-proof-subtle text-proof-dark'; 
                                        $status_text = '⏳ Chờ duyệt ảnh'; 
                                    } elseif ($row['status'] == 'pending') { 
                                        $status_badge = 'bg-warning-subtle text-warning-dark'; 
                                        $status_text = '🟠 Chờ xử lý'; 
                                    } elseif ($row['status'] == 'shipping') { 
                                        $status_badge = 'bg-info-subtle text-info-dark'; 
                                        $status_text = '🔵 Đang giao'; 
                                    } elseif ($row['status'] == 'completed') { 
                                        $status_badge = 'bg-success-subtle text-success-dark'; 
                                        $status_text = '🟢 Thành công'; 
                                    } elseif ($row['status'] == 'cancelled') { 
                                        $status_badge = 'bg-danger-subtle text-danger-dark'; 
                                        $status_text = '🔴 Đã hủy đơn'; 
                                    }
                                ?>
                                <tr class="text-center">
                                    <td class="fw-bold text-dark">#<?= $row['id'] ?></td>
                                    <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td class="text-start small">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['receiver_name'] ?: $row['full_name']) ?></div>
                                        <div class="text-muted text-truncate" style="max-width: 250px;"><?= htmlspecialchars($row['receiver_address'] ?: $row['address']) ?></div>
                                    </td>
                                    <td class="fw-bold text-danger"><?= number_format($row['total_money'], 0, ',', '.') ?>đ</td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2 fw-bold <?= $status_badge ?>" style="font-size: 11px;">
                                            <?= $status_text ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order-detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold small">
                                            <i class="bi bi-eye"></i> Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
.btn-gold { background-color: #E5A93B; border: none; transition: all 0.3s; }
.btn-gold:hover { background-color: #C98F2A; transform: translateY(-1px); }

/* Màu sắc trạng thái đồng bộ CSS Admin */
.bg-proof-subtle { background-color: #e2d9ff !important; }
.text-proof-dark { color: #4b23db !important; }
.bg-warning-subtle { background-color: #fff8e1 !important; }
.text-warning-dark { color: #b78103 !important; }
.bg-info-subtle { background-color: #e0f7fa !important; }
.text-info-dark { color: #006064 !important; }
.bg-success-subtle { background-color: #e8f5e9 !important; }
.text-success-dark { color: #1b5e20 !important; }
.bg-danger-subtle { background-color: #ffebee !important; }
.text-danger-dark { color: #b71c1c !important; }

.form-control:focus { border-color: #E5A93B !important; box-shadow: 0 0 0 0.25rem rgba(229, 169, 59, 0.15) !important; }
</style>

<?php include_once 'includes/footer.php'; ?>