<?php
// ============================================================
// File: admin-products.php
// Chức năng: Giao diện danh sách sản phẩm Admin Dashboard (PDO)
//            Tự động tính toán Top 3 sản phẩm bán chạy nhất từ hóa đơn
//            Tích hợp tính năng hiển thị & thiết lập Khuyến mãi trực tiếp
//            MỚI: Thêm chức năng xem, tăng, giảm số lượng tồn kho trực tiếp qua AJAX
// ============================================================
include_once '../config/db.php';
include_once 'includes/header.php'; // Sử dụng lại Navbar chung để đồng bộ

// 1. TỰ ĐỘNG CHỌN RA 3 SẢN PHẨM BÁN CHẠY NHẤT DỰA TRÊN SỐ LƯỢNG ĐÃ BÁN
$stmt_top3 = $pdo->query("SELECT od.product_id, SUM(od.quantity) as total_sold, p.name, p.image, p.price
                          FROM order_details od
                          JOIN products p ON od.product_id = p.id
                          GROUP BY od.product_id
                          ORDER BY total_sold DESC
                          LIMIT 3");
$top_selling_products = $stmt_top3->fetchAll();

// Mẹo lưu trữ nhanh ID top 3 sản phẩm bán chạy để đối chiếu nhanh phía dưới
$top3_ids = array_column($top_selling_products, 'product_id');

// Lấy danh sách danh mục để đổ vào thẻ Chọn (Select Option) trong Modal
$stmt_cats = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt_cats->fetchAll();

// Lấy toàn bộ danh sách sản phẩm kèm tên danh mục tương ứng (đảm bảo lấy thêm trường stock nếu có)
$stmt_prods = $pdo->query("SELECT p.*, c.name AS category_name FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            ORDER BY p.id DESC");
$products = $stmt_prods->fetchAll();
?>

<div class="container my-5 pt-4">
    <div id="ajaxAlertPlaceholder"></div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success_add'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Thêm sản phẩm mới thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'success_edit'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Cập nhật thông tin sản phẩm thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'success_delete'): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Đã xóa sản phẩm khỏi hệ thống cơ sở dữ liệu!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Bảng Điều Khiển Quản Trị</h3>
            <span class="badge bg-dark-subtle text-dark rounded-pill px-3 py-1.5 small text-uppercase">Quản lý sản phẩm</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-gold d-flex align-items-center fw-bold shadow-sm px-3 py-2 rounded-3 text-white btn-transition" onclick="openAddModal()">
                <i class="bi bi-plus-circle-fill me-2 fs-5"></i>Thêm Sản Phẩm Mới
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: linear-gradient(135deg, #FFF9E6 0%, #FFFFFF 100%);">
                <h6 class="fw-bold text-warning-dark mb-3"><i class="bi bi-trophy-fill me-2 text-warning"></i>Hệ thống tự động phân tích: Top 3 sản phẩm bán chạy nhất</h6>
                <div class="row g-3">
                    <?php if (!empty($top_selling_products)): ?>
                        <?php $rank = 1; foreach ($top_selling_products as $top_p): ?>
                            <div class="col-12 col-md-4">
                                <div class="d-flex align-items-center gap-3 bg-white p-2.5 rounded-3 border border-warning-subtle position-relative overflow-hidden">
                                    <div class="position-absolute top-0 start-0 bg-warning text-white fw-bold px-2 py-0.5 rounded-br" style="font-size: 11px; border-bottom-right-radius: 8px;">
                                        #<?= $rank++ ?>
                                    </div>
                                    <img src="../assets/img/<?= !empty($top_p['image']) ? $top_p['image'] : 'default.jpg'; ?>" class="rounded" style="width: 45px; height: 45px; object-fit: contain; background: #fafafa;">
                                    <div class="overflow-hidden" style="flex: 1;">
                                        <div class="fw-bold text-dark text-truncate small mb-0"><?= htmlspecialchars($top_p['name']) ?></div>
                                        <small class="text-success d-block fw-semibold" style="font-size: 11.5px;">Đã bán: <?= $top_p['total_sold'] ?> khối</small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-muted small ps-3">Chưa có dữ liệu đơn hàng mua sản phẩm để phân tích.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white table-responsive-custom double-scroll">
        <div class="table-responsive ">
            <table class="table table-hover align-middle mb-0" >
                <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="border-0 ps-4" style="width: 100px;">Hình ảnh</th>
                        <th class="border-0">Tên Sản Phẩm</th>
                        <th class="border-0">Phân Loại</th>
                        <th class="border-0 text-end" style="width: 150px;">Giá Bán hiện tại</th>
                        <th class="border-0 text-center" style="width: 160px;">Số Lượng Kho</th>
                        <th class="border-0 text-center">Định Vị</th>
                        <th class="border-0 text-center" style="width: 140px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="../assets/img/<?= !empty($prod['image']) ? $prod['image'] : 'default.jpg'; ?>" 
                                         class="rounded-3 border" style="width: 55px; height: 55px; object-fit: contain; background: #fafafa;" alt="<?= htmlspecialchars($prod['name']); ?>">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($prod['name']); ?></div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;"><?= htmlspecialchars($prod['short_desc'] ?? 'Chưa cấu hình mô tả cụ thể...'); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1.5 small">
                                        <?= htmlspecialchars($prod['category_name'] ?? 'Mặc định'); ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <?php if (isset($prod['is_on_sale']) && $prod['is_on_sale'] == 1 && !empty($prod['sale_price'])): ?>
                                        <div class="fw-bold text-danger fs-6"><?= number_format($prod['sale_price'], 0, ',', '.'); ?> đ</div>
                                        <small class="text-muted text-decoration-line-through" style="font-size: 12px;"><?= number_format($prod['price'], 0, ',', '.'); ?> đ</small>
                                    <?php else: ?>
                                        <div class="fw-bold text-dark fs-6"><?= number_format($prod['price'], 0, ',', '.'); ?> đ</div>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-light border rounded-pill p-1 shadow-sm">
                                        <button class="btn btn-sm btn-light rounded-circle p-0 d-flex align-items-center justify-content-center stock-btn" 
                                                style="width: 24px; height: 24px;" 
                                                onclick="changeStockInline(<?= $prod['id']; ?>, -1)">
                                            <i class="bi bi-dash fs-6 fw-bold"></i>
                                        </button>
                                        <span class="mx-3 fw-bold text-dark text-center" id="stock-val-<?= $prod['id']; ?>" style="min-width: 30px; font-size: 14px;">
                                            <?= isset($prod['stock']) ? $prod['stock'] : 0; ?>
                                        </span>
                                        <button class="btn btn-sm btn-light rounded-circle p-0 d-flex align-items-center justify-content-center stock-btn" 
                                                style="width: 24px; height: 24px;" 
                                                onclick="changeStockInline(<?= $prod['id']; ?>, 1)">
                                            <i class="bi bi-plus fs-6 fw-bold"></i>
                                        </button>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <?php if (isset($prod['is_on_sale']) && $prod['is_on_sale'] == 1): ?>
                                        <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small fw-bold shadow-sm animate-pulse"><i class="bi bi-tag-fill me-1"></i>Khuyến mãi</span>
                                    <?php elseif (in_array($prod['id'], $top3_ids)): ?>
                                        <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small fw-bold shadow-sm"><i class="bi bi-fire me-1"></i>Bán chạy nhất</span>
                                    <?php elseif (isset($prod['is_featured']) && $prod['is_featured'] == 1): ?>
                                        <span class="badge bg-warning-subtle text-warning-dark rounded-pill px-2.5 py-1 small fw-bold">Ghim nổi bật</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark rounded-pill px-2.5 py-1 small border">Thường</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group gap-1">
                                        <button class="btn btn-sm btn-outline-secondary rounded-3 border-0 px-2" 
                                                onclick='openEditModal(<?= json_encode($prod, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' title="Chỉnh sửa">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </button>
                                        <a href="admin-process.php?action=delete&id=<?= $prod['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-3 border-0 px-2" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này hoàn toàn khỏi Database?')" title="Xóa bỏ">
                                            <i class="bi bi-trash3 fs-5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted small">Cơ sở dữ liệu trống. Hãy nhấn nút phía trên để khởi tạo sản phẩm đầu tiên!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="productFormModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalComponentTitle">Thêm Sản Phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="admin-process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="productId">
                    <input type="hidden" name="old_image" id="productOldImage">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tên khối phô mai / Sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="name" id="productName" required placeholder="Ví dụ: Phô mai Parmesan Ý">
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Danh mục nhóm <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" name="category_id" id="productCategoryId" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Số lượng trong kho <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" name="stock" id="productStock" required min="0" value="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Giá gốc niêm yết (đ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" name="price" id="productPrice" required placeholder="180000">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-danger">Giá Khuyến Mãi (đ)</label>
                            <input type="number" class="form-control rounded-3 border-danger-subtle text-danger fw-bold" name="sale_price" id="productSalePrice" placeholder="Để trống nếu không giảm">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Hình ảnh sản phẩm mới</label>
                        <input type="file" class="form-control rounded-3" name="image" accept="image/*">
                        <div id="imageHelpBlock" class="form-text small text-muted">Nếu sửa và giữ nguyên ảnh cũ, không cần chọn tệp tại ô này.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mô tả tóm tắt</label>
                        <textarea class="form-control rounded-3" name="short_desc" id="productDesc" rows="3" placeholder="Nhập độ chín, xuất xứ hoặc cách chế biến khuyến nghị..."></textarea>
                    </div>

                    <div class="mb-2">
                        <div class="form-check form-switch py-1">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_on_sale" id="productIsOnSale" value="1">
                            <label class="form-check-label small fw-bold text-danger" for="productIsOnSale"><i class="bi bi-tags-fill me-1"></i>Bật trạng thái "Khuyến Mãi" trực tiếp</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch py-1">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_featured" id="productFeatured" value="1">
                            <label class="form-check-label small fw-bold text-dark" for="productFeatured">Đánh dấu là sản phẩm "Nổi bật" (Ghim danh mục)</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-3 text-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-gold text-white rounded-pill px-4">Xác nhận Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let modalInstance;

document.addEventListener("DOMContentLoaded", function() {
    modalInstance = new bootstrap.Modal(document.getElementById('productFormModal'));
});

function openAddModal() {
    document.getElementById('modalComponentTitle').innerText = "Thêm Sản Phẩm Khối Mới";
    document.getElementById('formAction').value = "add";
    document.getElementById('productId').value = "";
    document.getElementById('productOldImage').value = "";
    document.getElementById('productName').value = "";
    document.getElementById('productStock').value = "0"; // Mới
    document.getElementById('productPrice').value = "";
    document.getElementById('productSalePrice').value = "";
    document.getElementById('productDesc').value = "";
    document.getElementById('productIsOnSale').checked = false;
    document.getElementById('productFeatured').checked = false;
    document.getElementById('imageHelpBlock').style.display = "none";
    modalInstance.show();
}

function openEditModal(prod) {
    document.getElementById('modalComponentTitle').innerText = "Cập Nhật Thông Tin Sản Phẩm";
    document.getElementById('formAction').value = "edit";
    document.getElementById('productId').value = prod.id;
    document.getElementById('productOldImage').value = prod.image ? prod.image : "";
    document.getElementById('productName').value = prod.name;
    document.getElementById('productCategoryId').value = prod.category_id;
    document.getElementById('productStock').value = prod.stock ? prod.stock : 0; // Mới
    document.getElementById('productPrice').value = prod.price;
    document.getElementById('productSalePrice').value = prod.sale_price ? prod.sale_price : "";
    document.getElementById('productDesc').value = prod.short_desc ? prod.short_desc : "";
    
    document.getElementById('productIsOnSale').checked = (prod.is_on_sale == 1);
    document.getElementById('productFeatured').checked = (prod.is_featured == 1);
    document.getElementById('imageHelpBlock').style.display = "block";
    modalInstance.show();
}

// MỚI: Hàm JavaScript AJAX gửi yêu cầu tăng / giảm số lượng đến file admin-process.php mà không tải lại trang
function changeStockInline(productId, delta) {
    const stockSpan = document.getElementById(`stock-val-${productId}`);
    let currentStock = parseInt(stockSpan.innerText);
    let newStock = currentStock + delta;
    
    if (newStock < 0) {
        alert("Số lượng kho không thể nhỏ hơn 0!");
        return;
    }

    // Khởi tạo đối tượng FormData để gửi dữ liệu bằng phương thức POST
    let formData = new FormData();
    formData.append('action', 'update_stock_ajax');
    formData.append('id', productId);
    formData.append('stock', newStock);

    fetch('admin-process.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Thay đổi số liệu trên giao diện ngay lập tức
            stockSpan.innerText = newStock;
            
            // Hiện Toast/Thông báo nhanh thành công (tùy chọn)
            let alertBox = `
                <div class="alert alert-success alert-dismissible fade show rounded-3 small position-fixed top-0 end-0 m-3 shadow" style="z-index: 1060;" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>Đã cập nhật số lượng tồn kho thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            document.getElementById('ajaxAlertPlaceholder').innerHTML = alertBox;
            setTimeout(() => {
                const alertEl = document.querySelector('#ajaxAlertPlaceholder .alert');
                if(alertEl) {
                    let bsAlert = new bootstrap.Alert(alertEl);
                    bsAlert.close();
                }
            }, 2000);
        } else {
            alert("Lỗi: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Không thể kết nối đến máy chủ.");
    });
}
</script>

<style>
.btn-transition {
    transition: all 0.3s ease;
}
.btn-gold {
    background-color: #E5A93B;
    border: none;
    font-weight: 600;
}
.btn-gold:hover {
    background-color: #C98F2A;
    transform: translateY(-1.5px);
    box-shadow: 0 4px 12px rgba(229, 169, 59, 0.25) !important;
}
.text-warning-dark {
    color: #855d0a !important;
}
.border-warning-subtle {
    border-color: #ffeeba !important;
}
.stock-btn:hover {
    background-color: #e2e6ea !important;
    color: #E5A93B !important;
}
@keyframes pulse {
    0% { opacity: 0.85; }
    50% { opacity: 1; transform: scale(1.03); }
    100% { opacity: 0.85; }
}
.animate-pulse {
    animation: pulse 2s infinite ease-in-out;
}

</style>

<?php include_once 'includes/footer.php'; ?>