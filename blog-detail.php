<?php
// ============================================================
// File: blog-detail.php
// Chức năng: Giao diện xem chi tiết 1 bài viết theo Slug (PDO)
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

// Lấy slug từ thanh địa chỉ URL
$slug = $_GET['slug'] ?? '';

// Truy vấn cơ sở dữ liệu để tìm bài viết tương ứng
$stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ?");
$stmt->execute([$slug]);
$post = $stmt->fetch();

// Nếu không tìm thấy bài viết, chuyển hướng trả về trang blog chính
if (!$post) {
    header("Location: blog.php");
    exit();
}
?>

<div class="container my-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="blog.php" class="text-warning text-decoration-none fw-bold"><i class="bi bi-arrow-left me-1"></i> Quay lại Blog</a></li>
                    <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 250px;"><?= htmlspecialchars($post['title']); ?></li>
                </ol>
            </nav>

            <h1 class="fw-bold text-dark display-6 mb-3" data-aos="fade-up"><?= htmlspecialchars($post['title']); ?></h1>
            
            <div class="d-flex align-items-center text-muted small mb-4" data-aos="fade-up" data-aos-delay="50">
                <span class="me-3"><i class="bi bi-calendar3 text-warning me-1"></i> Đăng ngày: <?= date('d/m/Y H:i', strtotime($post['created_at'])); ?></span>
                <span><i class="bi bi-person text-warning me-1"></i> Tác giả: Ban Biên Tập</span>
            </div>

            <div class="rounded-4 overflow-hidden mb-5 border shadow-sm" style="max-height: 450px;" data-aos="zoom-in">
                <img src="assets/img/<?= !empty($post['image']) ? $post['image'] : 'default-post.jpg'; ?>" class="w-100 h-100 object-fit-cover" alt="">
            </div>

            <div class="post-content text-dark fs-6" style="line-height: 1.8; letter-spacing: 0.2px;" data-aos="fade-up">
                <?= nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>