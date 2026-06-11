<?php
// ============================================================
// File: admin-posts.php
// Chức năng: Giao diện quản lý bài viết Admin Dashboard (PDO)
// Phiên bản: Sửa lỗi triệt để hành động click nút bấm mở Modal
// ============================================================
include_once 'admin-check.php';
include_once '../config/db.php';
include_once 'includes/header.php';

// Lấy danh sách bài viết mới nhất xếp lên đầu
$stmt = $pdo->query("SELECT * FROM posts ORDER BY id DESC");
$posts = $stmt->fetchAll();
?>

<div class="container my-5 pt-4">
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success_add'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Thêm bài viết mới thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'success_edit'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Cập nhật bài viết thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'success_delete'): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-3 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Đã xóa bài viết khỏi hệ thống!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Quản Lý Bài Viết</h3>
            <span class="badge bg-dark-subtle text-dark rounded-pill px-3 py-1.5 small text-uppercase">Tin tức & Công thức món ăn</span>
        </div>
        <button type="button" class="btn btn-gold px-4 py-2 text-white shadow-sm" onclick="openAddPostModal()">
            <i class="bi bi-plus-circle me-2"></i>Viết Bài Mới
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white table-responsive-custom double-scroll">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="border-0 ps-4" style="width: 120px;">Hình ảnh</th>
                        <th class="border-0">Tiêu đề bài viết</th>
                        <th class="border-0">Tóm tắt ngắn</th>
                        <th class="border-0">Ngày đăng</th>
                        <th class="border-0 text-center" style="width: 140px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($posts) > 0): ?>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td class="ps-4">
                                    <img src="../assets/img/<?= !empty($post['image']) ? $post['image'] : 'default-post.jpg'; ?>" 
                                         class="rounded-3 border border-light shadow-sm" style="width: 70px; height: 50px; object-fit: cover;" alt="">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($post['title']); ?>">
                                        <?= htmlspecialchars($post['title']); ?>
                                    </div>
                                    <small class="text-muted d-block text-lowercase">/<?= htmlspecialchars($post['slug']); ?></small>
                                </td>
                                <td>
                                    <small class="text-muted d-block text-truncate" style="max-width: 300px;">
                                        <?= htmlspecialchars($post['summary'] ?? 'Không có tóm tắt...'); ?>
                                    </small>
                                </td>
                                <td class="small text-secondary">
                                    <?= date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 border-0 px-2" 
                                                onclick='openEditPostModal(<?= htmlspecialchars(json_encode($post, JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8"); ?>)' title="Sửa bài viết">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </button>
                                        <a href="admin-post-process.php?action=delete&id=<?= $post['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-3 border-0 px-2" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?')" title="Xóa bỏ">
                                            <i class="bi bi-trash3 fs-5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted small">Chưa có bài viết nào trong hệ thống.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="postFormModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalPostTitle">Viết Bài Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="admin-post-process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formPostAction" value="add">
                    <input type="hidden" name="id" id="postUserId">
                    <input type="hidden" name="old_image" id="postOldImage">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" name="title" id="postTitle" required onkeyup="generateSlug(this.value)" placeholder="Ví dụ: Cách làm pizza phô mai kéo sợi tại nhà">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Đường dẫn thân thiện (Slug)</label>
                        <input type="text" class="form-control rounded-3 bg-light" name="slug" id="postSlug" readonly placeholder="tu-dong-sinh-ra-duong-dan">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Hình ảnh đại diện</label>
                        <input type="file" class="form-control rounded-3" name="image" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tóm tắt bài viết (Hiển thị ở trang danh sách)</label>
                        <textarea class="form-control rounded-3" name="summary" id="postSummary" rows="2" placeholder="Nhập một đoạn ngắn khoảng 2-3 câu ngắn gọn..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nội dung chi tiết bài viết <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-3" name="content" id="postContent" rows="8" required placeholder="Nội dung kiến thức, công thức, bài viết chi tiết..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-3 text-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-gold text-white rounded-pill px-4">Lưu Bài Viết</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Hàm tự động tạo Slug từ tiêu đề Tiếng Việt có dấu
function generateSlug(val) {
    let slug = val.toLowerCase();
    slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
    slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
    slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
    slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
    slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
    slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
    slug = slug.replace(/đ/gi, 'd');
    slug = slug.replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    document.getElementById('postSlug').value = slug;
}

// Hàm xử lý bật Modal khi thêm mới bài viết (Khởi tạo trực tiếp khi click để chống lỗi nạp thiếu thư viện)
function openAddPostModal() {
    document.getElementById('modalPostTitle').innerText = "Viết Bài Thảo Luận Mới";
    document.getElementById('formPostAction').value = "add";
    document.getElementById('postUserId').value = "";
    document.getElementById('postOldImage').value = "";
    document.getElementById('postTitle').value = "";
    document.getElementById('postSlug').value = "";
    document.getElementById('postSummary').value = "";
    document.getElementById('postContent').value = "";
    
    // Kiểm tra tính sẵn sàng của Bootstrap JS trước khi kích hoạt hiển thị
    if (typeof bootstrap !== 'undefined') {
        let postFormModalEl = document.getElementById('postFormModal');
        let myModal = bootstrap.Modal.getInstance(postFormModalEl) || new bootstrap.Modal(postFormModalEl);
        myModal.show();
    } else {
        alert("Lỗi hệ thống: Chưa tìm thấy thư viện Bootstrap JavaScript! Vui lòng kiểm tra lại xem file header.php hoặc footer.php đã nhúng Bootstrap đúng cách chưa.");
    }
}

// Hàm xử lý đổ dữ liệu và bật Modal khi sửa bài viết
function openEditPostModal(post) {
    document.getElementById('modalPostTitle').innerText = "Chỉnh Sửa Nội Dung Bài Viết";
    document.getElementById('formPostAction').value = "edit";
    document.getElementById('postUserId').value = post.id;
    document.getElementById('postOldImage').value = post.image ? post.image : "";
    document.getElementById('postTitle').value = post.title;
    document.getElementById('postSlug').value = post.slug;
    document.getElementById('postSummary').value = post.summary ? post.summary : "";
    document.getElementById('postContent').value = post.content;
    
    // Kiểm tra tính sẵn sàng của Bootstrap JS trước khi kích hoạt hiển thị
    if (typeof bootstrap !== 'undefined') {
        let postFormModalEl = document.getElementById('postFormModal');
        let myModal = bootstrap.Modal.getInstance(postFormModalEl) || new bootstrap.Modal(postFormModalEl);
        myModal.show();
    } else {
        alert("Lỗi hệ thống: Chưa tìm thấy thư viện Bootstrap JavaScript! Vui lòng kiểm tra lại xem file header.php hoặc footer.php đã nhúng Bootstrap đúng cách chưa.");
    }
}
</script>

<style>
.btn-gold {
    background-color: #E5A93B !important;
    border: none !important;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-gold:hover {
    background-color: #C98F2A !important;
    transform: translateY(-1px);
}
</style>

<?php include_once 'includes/footer.php'; ?>