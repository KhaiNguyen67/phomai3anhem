<?php
// ============================================================
// File: includes/header.php
// Chức năng: Khởi tạo Session, thiết kế Header & CSS Glassmorphism
// ============================================================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tính tổng số lượng item thực tế trong giỏ hàng để hiển thị chính xác lên icon
$total_cart_items = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_cart_items += isset($item['quantity']) ? $item['quantity'] : 1;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phô Mai 3 Anh Em - Trải Nghiệm Ẩm Thực Cao Cấp</title>
    
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top glass-nav py-2">
    <div class="container">
        <!-- Thương hiệu / Logo: Tối ưu co giãn text và hình ảnh -->
        <a class="navbar-brand fw-bold fs-3 d-flex align-items-center text-decoration-none text-dark" href="index.php">
            <img src="assets/img/background3.jpg" alt="Logo Phô Mai 3 Anh Em" class="rounded-circle me-2 navbar-brand-img">
            <span class="brand-text">
                <span style="color: var(--primary-gold);">Phô Mai</span> 3 Anh Em
            </span>
        </a>
        
        <!-- Tổ hợp Tiện ích: Đưa Giỏ hàng ra ngoài đứng cạnh Hamburger khi thu nhỏ -->
        <div class="d-flex align-items-center gap-3 order-lg-last ms-auto me-3 me-lg-0">
            <a href="cart.php" class="position-relative text-dark fs-5 text-decoration-none me-1 hover-gold">
                <i class="bi bi-bag-heart"></i>
                <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?php echo $total_cart_items == 0 ? 'd-none' : ''; ?>" style="font-size: 0.65rem;">
                    <?php echo $total_cart_items; ?>
                </span>
            </a>
            
            <!-- Khu vực User: Ẩn bớt text, chỉ giữ nút chức năng icon gọn gàng trên Mobile -->
            <?php if(isset($_SESSION['user_name'])): ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin/index.php" class="btn btn-sm btn-gold rounded-pill px-2.5 px-sm-3" title="Quản trị">
                        <i class="bi bi-speedometer2"></i> <span class="d-none d-sm-inline ms-1">Quản Trị</span>
                    </a>
                <?php endif; ?>

                <span class="small fw-bold d-none d-md-inline text-secondary">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="change-password.php" class="btn btn-sm btn-outline-secondary rounded-pill" title="Đổi mật khẩu"><i class="bi bi-key"></i></a>
                <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill" title="Đăng xuất"><i class="bi bi-box-arrow-right"></i></a>
            <?php else: ?>
                <a href="login.php" class="btn btn-sm btn-outline-dark rounded-pill px-3">Đăng Nhập</a>
            <?php endif; ?>
        </div>

        <!-- Nút Hamburger nguyên bản -->
        <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Danh sách các danh mục điều hướng -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold text-center text-lg-start">
                <li class="nav-item px-1"><a class="nav-link text-dark" href="index.php">Trang Chủ</a></li>
                <li class="nav-item px-1"><a class="nav-link text-dark" href="product.php">Sản Phẩm</a></li>
                <li class="nav-item px-1"><a class="nav-link text-dark" href="index.php#about">Câu Chuyện</a></li>
                <li class="nav-item px-1"><a class="nav-link text-dark" href="Blog.php">Blog</a></li>
                <li class="nav-item px-1"><a class="nav-link text-dark" href="contact.php">Liên hệ</a></li>
            </ul>
        </div>
    </div>
</nav>

<main style="min-height: 75vh;">