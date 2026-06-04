<?php
// ============================================================
// File: admin-categories.php
// Chức năng: Giao diện danh sách danh mục Admin Dashboard (PDO)
// ============================================================
include_once '../config/db.php';
include_once 'includes/header.php'; // Sử dụng lại Navbar chung

// Lấy toàn bộ danh sách danh mục và đếm xem mỗi danh mục có bao nhiêu sản phẩm
$sql = "SELECT c.*, COUNT(p.id) AS total_products 
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id 
        GROUP BY c.id 
        ORDER BY c.id DESC";
$stmt = $pdo->query($sql);
$categories = $stmt->fetchAll();
?>

<div class="container my-5 pt-4">
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success_add'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Thêm danh mục mới thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'success_edit'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Cập nhật tên danh mục thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'success_delete'): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Đã xóa danh mục khỏi hệ thống!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'error_has_products'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-x-circle-fill me-2"></i>Không thể xóa! Danh mục này hiện đang có sản phẩm thuộc về nó.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Quản Lý Danh Mục</h3>
            <span class="badge bg-dark-subtle text-dark rounded-pill px-3 py-1.5 small text-uppercase">Phân loại phô mai</span>
        </div>
        <button class="btn btn-gold px-4 py-2 text-white" onclick="openAddModal()">
            <i class="bi bi-plus-circle me-2"></i>Thêm Danh Mục Mới
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="border-0 ps-4" style="width: 100px;">Mã ID</th>
                        <th class="border-0">Tên Danh Mục</th>
                        <th class="border-0 text-center">Số Lượng Sản Phẩm</th>
                        <th class="border-0 text-center" style="width: 140px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($categories) > 0): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?= $cat['id']; ?></td>
                                <td>
                                    <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($cat['name']); ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1.5 fw-bold">
                                        <?= $cat['total_products']; ?> sản phẩm
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group gap-1">
                                        <button class="btn btn-sm btn-outline-secondary rounded-3 border-0 px-2" 
                                                onclick='openEditModal(<?= json_encode($cat, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' title="Chỉnh sửa">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </button>
                                        <a href="admin-cat-process.php?action=delete&id=<?= $cat['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-3 border-0 px-2" 
                                           onclick="return confirm('Bạn chắc chắn muốn xóa danh mục này? Hãy đảm bảo danh mục không chứa sản phẩm nào.')" title="Xóa bỏ">
                                            <i class="bi bi-trash3 fs-5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted small">Chưa có danh mục nào. Hãy tạo mới ngay!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="categoryModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm"> <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalTitle">Thêm Danh Mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="admin-cat-process.php" method="POST">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="categoryId">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="name" id="categoryName" required placeholder="Ví dụ: Phô mai sợi">
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-3 text-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-gold text-white rounded-pill px-4">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let catModal;

document.addEventListener("DOMContentLoaded", function() {
    catModal = new bootstrap.Modal(document.getElementById('categoryModal'));
});

function openAddModal() {
    document.getElementById('modalTitle').innerText = "Thêm Danh Mục Mới";
    document.getElementById('formAction').value = "add";
    document.getElementById('categoryId').value = "";
    document.getElementById('categoryName').value = "";
    catModal.show();
}

function openEditModal(cat) {
    document.getElementById('modalTitle').innerText = "Sửa Tên Danh Mục";
    document.getElementById('formAction').value = "edit";
    document.getElementById('categoryId').value = cat.id;
    document.getElementById('categoryName').value = cat.name;
    catModal.show();
}
</script>

<style>
.btn-gold {
    background-color: #E5A93B;
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-gold:hover {
    background-color: #C98F2A;
    transform: translateY(-1px);
}
</style>

<?php include_once 'includes/footer.php'; ?>