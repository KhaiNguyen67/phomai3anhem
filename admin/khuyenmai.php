<?php
// ============================================================
// File: admin/khuyenmai.php
// Chức năng: Giao diện và xử lý thêm chương trình Khuyến mãi / Giảm giá
// ============================================================
include_once '../config/db.php';
include_once 'includes/header.php'; // Sử dụng lại bộ Header/Navbar chung

// Lấy danh sách danh mục để Admin lựa chọn áp dụng khuyến mãi theo nhóm (nếu cần)
$stmt_cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt_cats->fetchAll();

$message = "";
$msg_type = "";

// XỬ LÝ LƯU DỮ LIỆU KHI ADMIN ẤN NÚT TẠO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $promo_name = trim($_POST['promo_name']);
    $promo_code = strtoupper(trim($_POST['promo_code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $apply_to = $_POST['apply_to'];
    $category_id = ($apply_to == 'category') ? intval($_POST['category_id']) : null;
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Kiểm tra tính hợp lệ cơ bản
    if (empty($promo_name) || empty($promo_code) || $discount_value <= 0) {
        $message = "Vui lòng điền đầy đủ thông tin và giá trị giảm giá lớn hơn 0!";
        $msg_type = "danger";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $message = "Ngày bắt đầu không được lớn hơn ngày kết thúc chương trình!";
        $msg_type = "danger";
    } else {
        try {
            // Giả định bạn có bảng `promotions` trong CSDL
            $sql = "INSERT INTO promotions (name, code, discount_type, discount_value, apply_scope, category_id, start_date, end_date, is_active) 
                    VALUES (:name, :code, :discount_type, :discount_value, :apply_scope, :category_id, :start_date, :end_date, :is_active)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $promo_name,
                ':code' => $promo_code,
                ':discount_type' => $discount_type,
                ':discount_value' => $discount_value,
                ':apply_scope' => $apply_to,
                ':category_id' => $category_id,
                ':start_date' => $start_date,
                ':end_date' => $end_date,
                ':is_active' => $is_active
            ]);

            $message = "Khởi tạo chiến dịch khuyến mãi thành công!";
            $msg_type = "success";
        } catch (PDOException $e) {
            $message = "Lỗi hệ thống hoặc Trùng mã CODE: " . $e->getMessage();
            $msg_type = "danger";
        }
    }
}
?>

<div class="container my-5 pt-4">
    <div class="mb-4">
        <a href="admin-products.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại Quản lý sản phẩm
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi <?= $msg_type == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="glass-card p-4 p-md-5 shadow-lg">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                    <div class="icon-box bg-danger text-white rounded-3 p-3 shadow-sm">
                        <i class="bi bi-tags-fill fs-3"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark m-0">Thiết Lập Chiến Dịch Khuyến Mãi</h3>
                        <small class="text-muted">Tạo mã giảm giá hoặc chương trình ưu đãi giá cho các sản phẩm Phô mai</small>
                    </div>
                </div>

                <form action="khuyenmai.php" method="POST" class="needs-validation" novalidate>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-bold text-secondary">Tên chương trình khuyến mãi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 border-secondary-subtle" name="promo_name" required placeholder="Ví dụ: Tri ân mùa hè - Giảm giá Phô mai Sợi">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-secondary">Mã CODE áp dụng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 border-secondary-subtle fw-bold text-uppercase" name="promo_code" required placeholder="Ví dụ: NHIEUDAI3AE">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-secondary">Hình thức giảm giá</label>
                            <select class="form-select rounded-3 border-secondary-subtle" name="discount_type" id="discountType" onchange="updateValuePlaceholder()">
                                <option value="percentage">Giảm theo phần trăm (%)</option>
                                <option value="fixed">Giảm số tiền cố định (đ)</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-secondary">Mức giảm cụ thể <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control rounded-start-3 border-secondary-subtle" name="discount_value" id="discountValue" required placeholder="Ví dụ: 15" min="1">
                                <span class="input-group-text rounded-end-3 bg-light text-muted fw-bold" id="valueAddon">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-secondary">Phạm vi áp dụng</label>
                            <select class="form-select rounded-3 border-secondary-subtle" name="apply_to" id="applyTo" onchange="toggleCategorySelect()">
                                <option value="all">Toàn bộ sản phẩm phô mai cửa hàng</option>
                                <option value="category">Áp dụng riêng cho Nhóm Danh Mục</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6" id="categorySelectBlock" style="display: none;">
                            <label class="form-label small fw-bold text-secondary">Chọn danh mục phô mai hưởng ưu đãi</label>
                            <select class="form-select rounded-3 border-secondary-subtle" name="category_id">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-secondary"><i class="bi bi-calendar-check me-1"></i>Ngày bắt đầu chạy</label>
                            <input type="datetime-local" class="form-control rounded-3 border-secondary-subtle" name="start_date" required value="<?= date('Y-m-d\TH:i') ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-secondary"><i class="bi bi-calendar-x me-1"></i>Ngày kết thúc</label>
                            <input type="datetime-local" class="form-control rounded-3 border-secondary-subtle" name="end_date" required value="<?= date('Y-m-d\TH:i', strtotime('+7 days')) ?>">
                        </div>
                    </div>

                    <div class="mb-4 bg-light p-3 rounded-3 border">
                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActive" checked style="width: 2.5em; height: 1.25em;">
                            <label class="form-check-label small fw-bold text-dark" for="isActive">Kích hoạt chiến dịch ngay lập tức sau khi lưu</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="admin-products.php" class="btn btn-light rounded-pill px-4 text-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-danger text-white rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Phát hành Khuyến mãi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<style>
/* Phong cách thiết kế kính mờ Glassmorphism thống nhất toàn hệ thống */
.glass-card {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 20px;
}
.icon-box {
    background-color: #DC3545;
}
input[type="datetime-local"]:focus, select:focus, input[type="text"]:focus, input[type="number"]:focus {
    border-color: #DC3545 !important;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15) !important;
}
</style>

<script>
// Điều khiển giao diện thông minh bằng JavaScript tương tác người dùng
function updateValuePlaceholder() {
    const type = document.getElementById("discountType").value;
    const addon = document.getElementById("valueAddon");
    const input = document.getElementById("discountValue");

    if (type === "percentage") {
        addon.innerText = "%";
        input.placeholder = "Ví dụ: 15";
    } else {
        addon.innerText = "đ";
        input.placeholder = "Ví dụ: 50000";
    }
}

function toggleCategorySelect() {
    const applyTo = document.getElementById("applyTo").value;
    const block = document.getElementById("categorySelectBlock");

    if (applyTo === "category") {
        block.style.display = "block";
    } else {
        block.style.display = "none";
    }
}
</script>

<?php include_once 'includes/footer.php'; ?>