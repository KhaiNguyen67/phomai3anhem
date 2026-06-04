<?php
// ============================================================
// File: chitietblog.php
// Chức năng: Hiển thị nội dung chi tiết của một bài viết (Blog)
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

// 1. Lấy ID bài viết từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;

if ($id > 0) {
    // Truy vấn lấy dữ liệu bài viết theo ID
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $post = $stmt->fetch();
}

// 2. Lấy 3 bài viết liên quan (bài viết mới nhất khác bài hiện tại) để gợi ý
$stmt_related = $pdo->prepare("SELECT id, title, image, created_at FROM posts WHERE id != :id ORDER BY id DESC LIMIT 3");
$stmt_related->execute(['id' => $id]);
$related_posts = $stmt_related->fetchAll();

// Xử lý ảnh bìa
$cover_img = (!empty($post['image']) && file_exists('assets/img/'.$post['image']) && $post['image'] != 'default-post.jpg') 
             ? 'assets/img/'.$post['image'] 
             : 'https://images.unsplash.com/photo-1532634922-8fe0b757fb13?q=80&w=1200';
?>

<div class="container my-5 py-3">
    <?php if ($post): ?>
        <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-down">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-warning fw-bold">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-secondary">Góc chia sẻ</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page"><?php echo htmlspecialchars($post['title']); ?></li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-9" data-aos="fade-up">
                <div class="glass-card p-4 p-md-5 bg-white border-0 shadow-sm rounded-4">
                    
                    <div class="text-center mb-4">
                        <span class="badge bg-warning text-dark text-uppercase fw-bold px-3 py-2 rounded-pill mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Tin tức Ẩm thực</span>
                        <h1 class="fw-bold text-dark mb-3 lh-base" style="font-size: 2.2rem;">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h1>
                        <div class="text-muted small d-flex align-items-center justify-content-center gap-3">
                            <span><i class="bi bi-calendar3 me-1"></i> <?php echo date('d/m/Y - H:i', strtotime($post['created_at'])); ?></span>
                            <span><i class="bi bi-person-circle me-1"></i> Đăng bởi Admin</span>
                        </div>
                    </div>

                    <div class="lead fw-semibold text-secondary mb-4 pb-3 border-bottom text-justify">
                        <?php echo htmlspecialchars($post['summary']); ?>
                    </div>

                    <div class="mb-5 rounded-4 overflow-hidden shadow-sm">
                        <img src="<?php echo $cover_img; ?>" class="img-fluid w-100" style="max-height: 450px; object-fit: cover;" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>

                    <div class="post-content text-dark lh-lg text-justify" style="font-size: 1.05rem;">
                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 mt-5 pt-4 border-top">
                        <span class="fw-bold small text-muted text-uppercase me-2">Chia sẻ:</span>
                        <button class="btn btn-sm btn-outline-primary rounded-circle p-2" title="Facebook"><i class="bi bi-facebook fs-6"></i></button>
                        <button class="btn btn-sm btn-outline-info rounded-circle p-2" title="Twitter"><i class="bi bi-twitter-x fs-6"></i></button>
                        <button class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Pinterest"><i class="bi bi-pinterest fs-6"></i></button>
                    </div>

                </div>
            </div>
        </div>

        <?php if (count($related_posts) > 0): ?>
        <div class="mt-5 pt-5 border-top">
            <h4 class="fw-bold text-dark mb-4 text-center">BÀI VIẾT MỚI KHÁC</h4>
            <div class="row g-4 justify-content-center">
                <?php foreach($related_posts as $related): ?>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white product-card-hover">
                            <div style="height: 180px; overflow:hidden;">
                                <?php 
                                $rel_img = (!empty($related['image']) && file_exists('assets/img/'.$related['image']) && $related['image'] != 'default-post.jpg') 
                                            ? 'assets/img/'.$related['image'] 
                                            : 'https://images.unsplash.com/photo-1532634922-8fe0b757fb13?q=80&w=500';
                                ?>
                                <a href="chitietblog.php?id=<?php echo $related['id']; ?>">
                                    <img src="<?php echo $rel_img; ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                </a>
                            </div>
                            <div class="card-body p-4">
                                <small class="text-muted d-block mb-2"><i class="bi bi-calendar3 me-2"></i><?php echo date('d/m/Y', strtotime($related['created_at'])); ?></small>
                                <h6 class="fw-bold text-dark mb-0 text-truncate-2">
                                    <a href="chitietblog.php?id=<?php echo $related['id']; ?>" class="text-decoration-none text-dark hover-gold">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </a>
                                </h6>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="text-center py-5 my-5" data-aos="zoom-in">
            <i class="bi bi-file-earmark-x text-warning" style="font-size: 5rem;"></i>
            <h2 class="fw-bold text-dark mt-3">Không tìm thấy bài viết!</h2>
            <p class="text-muted mb-4">Bài viết bạn đang tìm kiếm có thể đã bị xóa hoặc đường dẫn không chính xác.</p>
            <a href="index.php" class="btn btn-gold rounded-pill px-4 py-2">Quay Lại Trang Chủ</a>
        </div>
    <?php endif; ?>
</div>

<style>
/* Căn đều hai bên cho nội dung dễ đọc */
.text-justify {
    text-align: justify;
}
/* Hiệu ứng zoom nhẹ ảnh trong phần bài viết liên quan */
.product-card-hover img {
    transition: transform 0.4s ease;
}
.product-card-hover:hover img {
    transform: scale(1.05);
}
/* CSS bổ trợ cho phần nội dung (giới hạn chiều rộng ảnh nếu người dùng up ảnh dọc) */
.post-content img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 15px 0;
}
</style>

<?php include_once 'includes/footer.php'; ?>