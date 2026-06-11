<?php 
// ============================================================
// File: admin/orders.php
// Chức năng: Quản lý danh sách đơn hàng cho Admin 
//            (Hiển thị thêm loại phương thức thanh toán)
// ============================================================
include_once 'admin-check.php';
include_once '../config/db.php'; 

include_once 'thongbaotrangthaidon.php';

// Xử lý AJAX đổi trạng thái Duyệt hoặc Hủy đơn hàng
if (isset($_POST['update_status'])) {
    header('Content-Type: application/json');
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        
        $stmt_user = $pdo->prepare("SELECT full_name, email FROM orders WHERE id = ?");
        $stmt_user->execute([$order_id]);
        $order_info = $stmt_user->fetch(PDO::FETCH_ASSOC);

        if ($order_info && !empty($order_info['email'])) {
            try {
                sendOrderNotification($order_info['email'], $order_info['full_name'], $order_id, $new_status);
            } catch (Exception $e) {
                // Ghi log lỗi mail nếu muốn
            }
        }
        
        if ($new_status === 'cancelled') {
            echo json_encode(['status' => 'deleted', 'message' => 'Đã hủy đơn hàng thành công!']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    }
    exit();
}

include_once 'includes/header.php'; 
?>

<div class="mb-4">
    <h4 class="fw-bold text-dark"><i class="bi bi-receipt me-2"></i>Hệ thống Quản lý Đơn Hàng</h4>
</div>

<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
    <div class="row g-3">
        <div class="col-12 col-md-7 col-lg-8">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3"><i class="bi bi-search"></i></span>
                <input type="text" id="searchOrderInput" class="form-control bg-light border-start-0 rounded-end-3 py-2 small" placeholder="Tìm theo mã đơn (VD: #12), tên khách hàng hoặc số điện thoại..." onkeyup="filterAdminOrders()">
            </div>
        </div>
        <div class="col-12 col-md-5 col-lg-4">
            <select id="filterStatusSelect" class="form-select bg-light rounded-3 py-2 small text-secondary" onchange="filterAdminOrders()">
                <option value="all">-- Tất cả trạng thái đơn --</option>
                <option value="pending">🟠 Chờ xử lý</option>
                <option value="shipping">🔵 Đang giao hàng</option>
                <option value="completed">🟢 Thành công</option>
            </select>
        </div>
    </div>
</div>

<div class="table-responsive-custom double-scroll bg-white rounded-3 shadow-sm p-3">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Mã ĐH</th>
                <th>Người Đặt (Mua)</th>
                <th>Người Nhận Hàng</th>
                <th>Tổng Tiền</th>
                <th>Phương Thức</th> <th>Ngày Đặt</th>
                <th>Trạng Thái</th>
                <th>Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Lấy những đơn hàng CHƯA BỊ HỦY để hiển thị ở trang Admin
            $stmt = $pdo->query("SELECT * FROM orders WHERE status != 'cancelled' ORDER BY id DESC");
            $has_orders = false;
            while ($row = $stmt->fetch()):
                $has_orders = true;
                
                $customer_search = text_clean($row['full_name'] . ' ' . ($row['receiver_name'] ?? ''));
                $phone_search = text_clean($row['phone'] . ' ' . ($row['receiver_phone'] ?? ''));
                
                $select_color_class = 'status-pending';
                if ($row['status'] == 'shipping') $select_color_class = 'status-shipping';
                if ($row['status'] == 'completed') $select_color_class = 'status-completed';

                // Chuẩn hóa tên hiển thị phương thức thanh toán
                $pm = strtolower(trim($row['payment_method'] ?? 'cod'));
                if ($pm === 'vnpay') {
                    $payment_badge = '<span class="badge bg-vnpay-light text-vnpay fw-bold rounded-pill px-2.5 py-1.5 small"><i class="bi bi-credit-card-2-front-fill me-1"></i>VNPAY</span>';
                } elseif ($pm === 'bank' || $pm === 'transfer') {
                    $payment_badge = '<span class="badge bg-warning-light text-warning-dark fw-bold rounded-pill px-2.5 py-1.5 small"><i class="bi bi-bank2 me-1"></i>BANK</span>';
                } else {
                    $payment_badge = '<span class="badge bg-secondary-light text-secondary-dark fw-bold rounded-pill px-2.5 py-1.5 small"><i class="bi bi-truck me-1"></i>COD</span>';
                }
            ?>
            <tr class="order-data-row" 
                id="order-row-<?= $row['id'] ?>"
                data-id="<?= $row['id'] ?>" 
                data-customer="<?= htmlspecialchars($customer_search) ?>" 
                data-phone="<?= htmlspecialchars($phone_search) ?>" 
                data-payment="<?= $pm ?>"
                data-status="<?= $row['status'] ?>">
                
                <td class="fw-bold">#<?= $row['id'] ?></td>
                <td>
                    <div class="fw-semibold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($row['phone']) ?></small>
                </td>
                <td>
                    <div class="fw-semibold text-warning">
                        <?= htmlspecialchars($row['receiver_name'] ?: $row['full_name']) ?>
                    </div>
                    <small class="text-muted d-block">
                        <i class="bi bi-telephone small"></i> <?= htmlspecialchars($row['receiver_phone'] ?: $row['phone']) ?>
                    </small>
                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;" title="<?= htmlspecialchars($row['receiver_address'] ?: $row['address']) ?>">
                        <i class="bi bi-geo-alt small"></i> <?= htmlspecialchars($row['receiver_address'] ?: $row['address']) ?>
                    </small>
                </td>
                <td class="fw-bold text-danger"><?= number_format($row['total_money'], 0, ',', '.') ?>đ</td>
                
                <td><?= $payment_badge ?></td>

                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                
                <td>
                    <select class="form-select form-select-sm rounded-pill px-3 fw-bold change-order-status <?= $select_color_class ?>" 
                            data-id="<?= $row['id'] ?>" style="width: 160px; font-size: 11px;">
                        <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>🟠 CHỜ XỬ LÝ</option>
                        <option value="shipping" <?= $row['status'] == 'shipping' ? 'selected' : '' ?>>🔵 ĐANG GIAO</option>
                        <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>🟢 THÀNH CÔNG</option>
                        <option value="cancelled">🔴 HỦY ĐƠN HÀNG</option>
                    </select>
                </td>
                <td>
                    <a href="orders-detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-eye"></i> Chi tiết
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            
            <tr id="noResultsRow" style="display: <?= $has_orders ? 'none' : '' ?>;">
                <td colspan="8" class="text-center text-muted py-4 small">
                    <i class="bi bi-inbox display-6 d-block mb-2 text-secondary"></i>
                    Không tìm thấy dữ liệu đơn hàng nào trùng khớp.
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php 
function text_clean($str) {
    return mb_strtolower(trim($str), 'UTF-8');
}
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    initStatusChangeEvents();
});

function triggerDeleteOrder(orderId, selectElement) {
    if (confirm(`⚠️ Bạn có chắc chắn muốn hủy đơn hàng #${orderId}?\nĐơn hàng này sẽ biến mất khỏi mục quản lý của bạn nhưng khách hàng vẫn có thể thấy thông báo đã hủy.`)) {
        let parentRow = document.getElementById(`order-row-${orderId}`);
        
        let formData = new FormData();
        formData.append('update_status', true);
        formData.append('order_id', orderId);
        formData.append('status', 'cancelled');

        fetch('orders.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'deleted') {
                parentRow.style.transition = "all 0.4s ease";
                parentRow.style.opacity = "0";
                parentRow.style.transform = "translateX(50px)";
                setTimeout(() => {
                    parentRow.remove();
                    filterAdminOrders();
                }, 400);
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(err => {
            console.error("Lỗi:", err);
            alert('Không thể kết nối đến máy chủ.');
        });
    } else {
        window.location.reload();
    }
}

function initStatusChangeEvents() {
    document.querySelectorAll('.change-order-status').forEach(select => {
        select.dataset.oldValue = select.value; 

        select.addEventListener('change', function() {
            let orderId = this.getAttribute('data-id');
            let statusVal = this.value;
            let self = this;
            let parentRow = self.closest('.order-data-row');

            if (statusVal === 'cancelled') {
                triggerDeleteOrder(orderId, self);
                return;
            }

            parentRow.setAttribute('data-status', statusVal);

            self.classList.remove('status-pending', 'status-shipping', 'status-completed');
            if (statusVal === 'pending') self.classList.add('status-pending');
            if (statusVal === 'shipping') self.classList.add('status-shipping');
            if (statusVal === 'completed') self.classList.add('status-completed');

            let formData = new FormData();
            formData.append('update_status', true);
            formData.append('order_id', orderId);
            formData.append('status', statusVal);

            fetch('orders.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => { 
                if(data.status === 'success') {
                    self.dataset.oldValue = statusVal;
                    let originBg = parentRow.style.backgroundColor;
                    parentRow.style.backgroundColor = 'rgba(25, 135, 84, 0.12)';
                    setTimeout(() => { parentRow.style.backgroundColor = originBg; }, 400);
                } 
            })
            .catch(err => {
                alert('Không thể kết nối đến máy chủ.');
            });
        });
    });
}

function filterAdminOrders() {
    let keyword = document.getElementById('searchOrderInput').value.toLowerCase().trim();
    let selectedStatus = document.getElementById('filterStatusSelect').value;
    let rows = document.querySelectorAll('.order-data-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let idAttr = row.getAttribute('data-id').toLowerCase();
        let customerAttr = row.getAttribute('data-customer');
        let phoneAttr = row.getAttribute('data-phone');
        let statusAttr = row.getAttribute('data-status');
        
        let matchKeyword = (idAttr.includes(keyword) || customerAttr.includes(keyword) || phoneAttr.includes(keyword));
        let matchStatus = (selectedStatus === 'all' || statusAttr === selectedStatus);
        
        if (matchKeyword && matchStatus) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    });
    
    let noResultsRow = document.getElementById('noResultsRow');
    if (noResultsRow) {
        noResultsRow.style.display = visibleCount === 0 ? "" : "none";
    }
}
</script>

<style>
.change-order-status { border: 1px solid transparent !important; cursor: pointer; }
.status-pending { background-color: #fff3cd !important; color: #664d03 !important; border-color: #ffecb5 !important; }
.status-shipping { background-color: #cff4fc !important; color: #087990 !important; border-color: #b6effb !important; }
.status-completed { background-color: #d1e7dd !important; color: #0f5132 !important; border-color: #badbcc !important; }

/* Thêm định dạng màu sắc cho hệ thống Badge phương thức thanh toán */
.bg-vnpay-light { background-color: #e6f2ff !important; }
.text-vnpay { color: #0066cc !important; }
.bg-warning-light { background-color: #fff0e6 !important; }
.text-warning-dark { color: #d97706 !important; }
.bg-secondary-light { background-color: #f1f5f9 !important; }
.text-secondary-dark { color: #475569 !important; }

.form-select:focus, .form-control:focus {
    border-color: #E5A93B !important;
    box-shadow: 0 0 0 0.25rem rgba(229, 169, 59, 0.15) !important;
}
.order-data-row { transition: background-color 0.3s ease, transform 0.4s ease, opacity 0.4s ease; }
</style>

<?php include_once 'includes/footer.php'; ?>