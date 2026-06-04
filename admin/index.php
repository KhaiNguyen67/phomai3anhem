<?php 
// ============================================================
// File: admin/index.php
// Chức năng: Trang tổng quan thống kê dữ liệu đồng bộ với CSDL mới
//            (Đã loại bỏ biểu đồ doanh thu, ẩn đơn hàng tại chỗ khi bấm đã xem)
// ============================================================
include_once 'admin-check.php';
include_once '../config/db.php'; 
include_once 'includes/header.php'; 

// 1. Tính toán các con số tổng quan
$total_revenue = $pdo->query("SELECT SUM(total_money) FROM orders WHERE status = 'completed'")->fetchColumn() ?? 0;
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$new_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark fs-4 fs-md-3">Trang Tổng Quan Thống Kê</h3>
        <small class="text-muted">Báo cáo số liệu và danh sách đơn hàng mới cần xử lý</small>
    </div>
    <span class="badge bg-white text-dark border p-2.5 rounded-3 shadow-sm align-self-stretch align-self-sm-auto text-center">
        <i class="bi bi-calendar3 me-2 text-warning"></i>Hôm nay: <?php echo date('d/m/Y'); ?>
    </span>
</div>

<div class="row g-3 g-md-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-card p-4 bg-white border-0 shadow-sm rounded-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="overflow-hidden">
                    <h6 class="text-muted small text-uppercase fw-bold text-truncate" style="font-size: 11px; letter-spacing: 0.5px;">Tổng doanh thu</h6>
                    <h3 class="fw-bold text-success m-0 text-truncate fs-4 fs-md-3"><?php echo number_format($total_revenue, 0, ',', '.'); ?>đ</h3>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success fs-3 lh-1 flex-shrink-0">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-card p-4 bg-white border-0 shadow-sm rounded-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="overflow-hidden">
                    <h6 class="text-muted small text-uppercase fw-bold text-truncate" style="font-size: 11px; letter-spacing: 0.5px;">Tổng đơn hàng</h6>
                    <h3 class="fw-bold text-dark m-0 fs-4 fs-md-3"><?php echo $total_orders; ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary fs-3 lh-1 flex-shrink-0">
                    <i class="bi bi-cart3"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-card p-4 bg-white border-0 shadow-sm rounded-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="overflow-hidden">
                    <h6 class="text-muted small text-uppercase fw-bold text-truncate" style="font-size: 11px; letter-spacing: 0.5px;">Đơn mới chờ duyệt</h6>
                    <h3 class="fw-bold text-danger m-0 fs-4 fs-md-3"><?php echo $new_orders; ?></h3>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger fs-3 lh-1 flex-shrink-0">
                    <i class="bi bi-bell-fill <?php echo $new_orders > 0 ? 'animate-bell' : ''; ?>"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-card p-4 bg-white border-0 shadow-sm rounded-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div class="overflow-hidden">
                    <h6 class="text-muted small text-uppercase fw-bold text-truncate" style="font-size: 11px; letter-spacing: 0.5px;">Sản phẩm phô mai</h6>
                    <h3 class="fw-bold text-warning m-0 fs-4 fs-md-3"><?php echo $total_products; ?></h3>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning fs-3 lh-1 flex-shrink-0">
                    <i class="bi bi-egg-fried"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5"> 
    <div class="col-12">
        <div class="card admin-card p-3 p-md-4 bg-white border-0 shadow-sm rounded-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1 fs-5"><i class="bi bi-clock-history text-warning me-2"></i>Đơn hàng mới nhất</h5>
                    <small class="text-muted">Hiển thị các đơn hàng vừa phát sinh trên hệ thống</small>
                </div>
                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5" id="visible-count-badge">Hiển thị: 5 đơn gần đây</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="table-light text-secondary small text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                        <tr>
                            <th class="border-0 ps-3" style="width: 100px;">Mã ĐH</th>
                            <th class="border-0" style="min-width: 180px;">Khách hàng</th>
                            <th class="border-0 text-center" style="width: 150px;">Trạng thái</th>
                            <th class="border-0 text-end pe-3" style="width: 160px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="latest-orders-tbody">
                        <?php
                        // Lấy chuẩn 5 đơn hàng mới nhất ra hiển thị
                        $latest_orders = $pdo->query("SELECT id, full_name, status FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();
                        if (count($latest_orders) > 0):
                            foreach($latest_orders as $o):
                        ?>
                        <tr id="order-row-<?php echo $o['id']; ?>" class="order-item-row" style="transition: all 0.35s ease;">
                            <td class="ps-3"><strong>#<?php echo $o['id']; ?></strong></td>
                            <td class="fw-medium text-dark"><?php echo htmlspecialchars($o['full_name']); ?></td>
                            <td class="text-center">
                                <?php if ($o['status'] == 'pending'): ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">Chờ duyệt</span>
                                <?php elseif ($o['status'] == 'completed'): ?>
                                    <span class="badge bg-success text-white rounded-pill px-2.5 py-1">Thành công</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1"><?php echo htmlspecialchars($o['status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-light border text-secondary rounded-3 px-2.5 py-1 hover-hide-btn" title="Ẩn tạm thời khỏi màn hình tổng quan" onclick="dismissOrderRow(<?php echo $o['id']; ?>)">
                                    <i class="bi bi-eye-slash me-1"></i>Đã xem
                                </button>
                            </td>
                        </tr>
                        <?php 
                            endforeach; 
                        else:
                        ?>
                        <tr id="no-orders-placeholder">
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-black-50"></i>
                                Chưa có đơn hàng nào trên hệ thống.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pt-3 border-top mt-3 text-center">
                <a href="orders.php" class="btn btn-sm btn-outline-secondary px-4 py-2 rounded-3 fw-semibold shadow-sm text-decoration-none">
                    <i class="bi bi-arrow-right-circle me-2"></i>Xem Toàn Bộ Chi Tiết Quản Lý Đơn Hàng
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes bell-ring {
    0%, 100% { transform: rotate(0); }
    15% { transform: rotate(10deg); }
    30% { transform: rotate(-10deg); }
    45% { transform: rotate(5deg); }
    60% { transform: rotate(-5deg); }
}
.animate-bell {
    display: inline-block;
    animation: bell-ring 2.5s infinite ease-in-out;
}
.hover-hide-btn:hover {
    background-color: #f0f0f0 !important;
    color: #dc3545 !important;
    border-color: #ddd !important;
}
</style>

<script>
// Hàm JavaScript xử lý ẩn tạm thời hàng đơn hàng ngay tại giao diện index
function dismissOrderRow(orderId) {
    const row = document.getElementById('order-row-' + orderId);
    if (row) {
        // Thực hiện hiệu ứng mờ dần và thu nhỏ dòng trước khi ẩn hẳn
        row.style.opacity = '0';
        row.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            row.remove(); // Xóa thẻ khỏi cây DOM HTML của trang index hiện tại
            
            // Tính toán lại số lượng hàng còn lại hiển thị trong bảng
            const remainingRows = document.querySelectorAll('.order-item-row');
            const badge = document.getElementById('visible-count-badge');
            
            if (badge) {
                badge.innerText = 'Hiển thị: ' + remainingRows.length + ' đơn gần đây';
            }
            
            // Nếu admin đã bấm ẩn sạch cả 5 dòng, hiển thị thông báo trống dữ liệu tạm thời
            if (remainingRows.length === 0) {
                const tbody = document.getElementById('latest-orders-tbody');
                if (tbody) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="bi bi-check2-all fs-2 d-block mb-2 text-success"></i>
                                Bạn đã xem hết các đơn hàng hiển thị nhanh ở đây.
                            </td>
                        </tr>
                    `;
                }
            }
        }, 350); // Khớp với thời gian transition CSS
    }
}

// Kích hoạt sáng đèn menu Tổng quan trên Sidebar điều hướng hệ thống
document.getElementById('side-dashboard').classList.add('active');
</script>

<?php include_once 'includes/footer.php'; ?>