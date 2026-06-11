<?php
// ============================================================
// File: blog.php
// Chức năng: Giao diện danh sách bài viết & tin tức cho khách hàng
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

// 1. Lấy danh sách toàn bộ bài viết (Mới nhất lên đầu)
$stmt_all = $pdo->query("SELECT * FROM posts ORDER BY id DESC");
$posts = $stmt_all->fetchAll();

// 2. Lấy 3 bài viết mới nhất để làm mục "Bài viết nổi bật" ở Sidebar
$stmt_recent = $pdo->query("SELECT * FROM posts ORDER BY id DESC LIMIT 3");
$recent_posts = $stmt_recent->fetchAll();
?>

<div class="blog-banner py-5 mb-5 bg-light position-relative overflow-hidden" style="background: linear-gradient(135deg, #fdfbf7 0%, #f5eedc 100%);">
    <div class="container py-4 text-center" data-aos="fade-down">
        <h1 class="fw-bold text-dark display-5 mb-2">Góc Chia Sẻ Kiến Thức</h1>
        <p class="text-muted lead mb-0">Khám phá công thức nấu ăn, mẹo bảo quản và câu chuyện thú vị về thế giới phô mai.</p>
        <div class="mx-auto mt-3 bg-gold rounded-pill" style="width: 60px; height: 4px;"></div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-4">
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-6 col-12" data-aos="fade-up">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden blog-card bg-white">
                                <div class="position-relative overflow-hidden" style="height: 200px;">
                                    <img src="assets/img/<?= !empty($post['image']) ? $post['image'] : 'default-post.jpg'; ?>" 
                                         class="w-100 h-100 object-fit-cover blog-img" 
                                         alt="<?= htmlspecialchars($post['title']); ?>">
                                    <span class="position-absolute bottom-0 start-0 bg-gold text-white px-3 py-1 small rounded-tr-4 font-semibold shadow-sm">
                                        <i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y', strtotime($post['created_at'])); ?>
                                    </span>
                                </div>
                                
                                <div class="card-body p-4 d-flex flex-column">
                                    <h5 class="fw-bold text-dark mb-2 line-clamp-2 hover-gold-text">
                                        <a href="blog-detail.php?slug=<?= $post['slug']; ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h5>
                                    <p class="text-muted small mb-4 flex-grow-1 line-clamp-3">
                                        <?= htmlspecialchars($post['summary'] ?? 'Bấm xem chi tiết để khám phá những kiến thức bổ ích về món ăn này...'); ?>
                                    </p>
                                    <div class="mt-auto">
                                        <a href="blog-detail.php?slug=<?= $post['slug']; ?>" class="btn btn-sm btn-outline-gold rounded-pill px-3 fw-bold small transition-all">
                                            Đọc tiếp <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="text-muted mb-3"><i class="bi bi-journal-x display-1"></i></div>
                        <h5 class="text-secondary fw-bold">Hiện chưa có bài viết nào được đăng tải</h5>
                        <p class="small text-muted">Vui lòng quay lại sau để cập nhật những tin tức mới nhất từ hệ thống.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" data-aos="fade-left">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Về Phô Mai 3 Anh Em</h5>
                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    Nơi kết nối đam mê ẩm thực ẩm thực cao cấp. Chúng tôi cung cấp những khối phô mai nhập khẩu chính hãng hảo hạng nhất cùng cẩm nang chế biến chuẩn bếp Âu.
                </p>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white position-sticky" style="top: 90px;" data-aos="fade-left" data-aos-delay="100">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Bài Viết Mới Nhất</h5>
                <div class="d-flex flex-column gap-3">
                    <?php if (count($recent_posts) > 0): ?>
                        <?php foreach ($recent_posts as $r_post): ?>
                            <div class="d-flex align-items-center gap-3 pb-2 border-bottom border-light">
                                <img src="assets/img/<?= !empty($r_post['image']) ? $r_post['image'] : 'default-post.jpg'; ?>" 
                                     class="rounded-3 object-fit-cover shadow-sm" style="width: 65px; height: 50px;" alt="">
                                <div class="overflow-hidden">
                                    <h6 class="text-dark small fw-bold mb-1 text-truncate">
                                        <a href="blog-detail.php?slug=<?= $r_post['slug']; ?>" class="text-decoration-none text-dark hover-gold-text">
                                            <?= htmlspecialchars($r_post['title']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted d-block" style="font-size: 11px;">
                                        <i class="bi bi-clock me-1"></i><?= date('d/m/Y', strtotime($r_post['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <small class="text-muted">Chưa có bài viết mới cập nhật.</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --gold-color: #E5A93B;
    --gold-hover: #C98F2A;
}
.bg-gold {
    background-color: var(--gold-color) !important;
}
.btn-outline-gold {
    color: var(--gold-color);
    border-color: var(--gold-color);
}
.btn-outline-gold:hover {
    background-color: var(--gold-color);
    border-color: var(--gold-color);
    color: #fff;
}
.hover-gold-text a:hover {
    color: var(--gold-color) !important;
}
.blog-card {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.blog-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 20px rgba(0,0,0,0.08) !important;
}
.blog-img {
    transition: transform 0.5s ease;
}
.blog-card:hover .blog-img {
    transform: scale(1.06);
}
.rounded-tr-4 {
    border-top-right-radius: 12px !important;
}
/* Giới hạn số dòng text để giao diện đều tăm tắp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;  
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;  
    overflow: hidden;
}
</style>

<?php include_once 'includes/footer.php'; ?>