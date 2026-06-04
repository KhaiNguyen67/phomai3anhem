<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
// Bảo mật: Chặn nếu không phải là admin (Dựa trên database.sql của bạn)
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { header('Location: ../index.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <!-- Quan trọng: Bắt buộc phải có để kích hoạt Responsive trên điện thoại -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản trị - Phô Mai 3 Anh Em</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        :root { 
            --sidebar-width: 260px; 
            --gold-color: #cca43b; 
            --dark-bg: #1e1e2d;
        }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        /* ── BỐ CỤC SIDEBAR TRÊN PC (MẶC ĐỊNH) ── */
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            position: fixed; 
            top: 0; 
            left: 0; 
            background: var(--dark-bg); 
            z-index: 1040; 
            transition: all 0.3s ease; 
        }
        .sidebar .nav-link { color: #a2a3b7; padding: 12px 25px; display: flex; align-items: center; gap: 12px; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: #1b1b28; border-left: 4px solid var(--gold-color); }
        
        .main-content { margin-left: var(--sidebar-width); padding: 30px; min-height: 100vh; transition: all 0.3s ease; }
        .admin-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

        /* Ẩn nút bấm Mobile trên màn hình lớn */
        .mobile-header-bar { display: none; }

        /* ── CSS RESPONSIVE CHO THIẾT BỊ DI ĐỘNG & MÁY TÍNH BẢNG (DƯỚI 992PX) ── */
        @media (max-width: 991.98px) {
            /* 1. Biến Sidebar thành Menu ẩn kéo từ trên xuống (hoặc dùng Collapse của Bootstrap) */
            .sidebar {
                width: 100%;
                height: auto;
                max-height: 100vh;
                position: fixed;
                top: 56px; /* Nằm ngay dưới thanh mobile header */
                left: 0;
                overflow-y: auto;
                display: none !important; /* Ẩn mặc định, điều khiển qua lớp .show của Bootstrap */
                padding-bottom: 20px;
                box-shadow: 0 10px 15px rgba(0,0,0,0.1);
            }
            
            /* Hiển thị sidebar lên khi bấm nút Hamburger */
            .sidebar.show {
                display: flex !important;
            }

            .sidebar .nav-link {
                padding: 14px 20px;
                border-left: none !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            }
            .sidebar .nav-link:hover, .sidebar .nav-link.active {
                background: #151521;
                color: var(--gold-color);
            }

            /* 2. Giải phóng không gian nội dung chính, đẩy sát lề */
            .main-content { 
                margin-left: 0; 
                padding: 20px 15px; 
                padding-top: 80px; /* Tạo khoảng trống để không bị thanh header trên đỉnh đè chữ */
            }

            /* 3. BẬT THANH HEADER TRÊN ĐỈNH MÀN HÌNH CHỨA NÚT HAMBURGER */
            .mobile-header-bar {
                display: flex;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 56px;
                background: var(--dark-bg);
                z-index: 1050;
                padding: 0 15px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }

            .navbar-toggler:focus {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<!-- THANH HEADER TRÊN ĐỈNH (CHỈ HIỂN THỊ TRÊN ĐIỆN THOẠI & TABLET) -->
<div class="mobile-header-bar align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <h6 class="text-white fw-bold text-uppercase m-0" style="letter-spacing: 0.5px; font-size: 14px;">3 Anh Em Admin</h6>
    </div>
    
    <!-- Nút Hamburger kích hoạt Menu dọc -->
    <button class="navbar-toggler text-white border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#adminSidebarContent" aria-controls="adminSidebarContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list fs-2"></i>
    </button>
</div>

<!-- THANH SIDEBAR (TỰ ĐỘNG BIẾN ĐỔI THEO THIẾT BỊ) -->
<div class="sidebar collapse d-flex flex-column pt-3" id="adminSidebarContent">
    <!-- Tiêu đề ẩn bớt trên mobile cho gọn -->
    <div class="text-center mb-4 px-3 d-none d-lg-block">
        <h5 class="text-white fw-bold text-uppercase m-0" style="letter-spacing: 1px;">3 Anh Em Admin</h5>
        <small class="text-muted">Hệ thống quản lý cửa hàng</small>
    </div>
    <hr class="border-secondary opacity-25 mx-3 d-none d-lg-block">
    
    <!-- Các mục chức năng điều hướng -->
    <ul class="nav flex-column flex-grow-1">
        <li class="nav-item"><a href="index.php" class="nav-link" id="side-dashboard"><i class="bi bi-speedometer2"></i> Trang tổng quan</a></li>
        <li class="nav-item"><a href="admin-products.php" class="nav-link" id="side-products"><i class="bi bi-box-seam"></i> Quản Lý Sản Phẩm</a></li>
        <li class="nav-item"><a href="admin-categories.php" class="nav-link" id="side-categories"><i class="bi bi-tags"></i> Quản lý Danh mục</a></li>
        <li class="nav-item"><a href="orders.php" class="nav-link" id="side-orders"><i class="bi bi-receipt"></i> Quản lý Đơn hàng</a></li>
        <li class="nav-item"><a href="admin-users.php" class="nav-link" id="side-users"><i class="bi bi-people"></i> Quản lý Người dùng</a></li>
        <li class="nav-item"><a href="admin-posts.php" class="nav-link" id="side-posts"><i class="bi bi-journal-text"></i> Quản lý Bài viết</a></li>
        <li class="nav-item"><a href="messages.php" class="nav-link" id="side-posts"><i class="bi bi-journal-text"></i>Phản hồi từ khách hàng</a></li>
    </ul>
    
    <!-- Khu vực liên kết hệ thống ở đáy -->
    <div class="p-3 mt-auto">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-white-50 border-secondary"><i class="bi bi-arrow-left"></i> Xem Website</a>
        <a href="../logout.php" class="btn btn-sm btn-danger w-100"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
    </div>
</div>

<!-- KHU VỰC CHỨA NỘI DUNG CHÍNH CỦA CÁC TRANG CON -->
<div class="main-content">
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>