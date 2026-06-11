<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
// admin/includes/header.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản trị - Phô Mai 3 Anh Em</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
    :root { 
        --sidebar-width: 240px; /* Thu gọn sidebar từ 260px xuống 240px để tăng diện tích hiển thị */
        --gold-color: #cca43b; 
        --dark-bg: #1e1e2d;
    }

    /* ── THIẾT LẬP NỀN TẢNG CHỐNG TRÀN KHUNG TOÀN TRANG ── */
    * {
        box-sizing: border-box;
    }

    body { 
        background-color: #f8f9fa; 
        font-family: 'Segoe UI', system-ui, sans-serif; 
        overflow-x: hidden; 
        width: 100%;
    }

    img {
        max-width: 100%;
        height: auto;
    }

    /* ── BỐ CỤC SIDEBAR TRÊN PC (ĐÃ THU GỌN VÀ TINH CHỈNH ĐỘ CAO) ── */
    .sidebar { 
        width: var(--sidebar-width); 
        height: 100vh; 
        position: fixed; 
        top: 0; 
        left: 0; 
        background: var(--dark-bg); 
        z-index: 1040; 
        transition: all 0.3s ease; 
        display: flex;
        flex-direction: column;
    }

    /* Thu nhỏ padding của nav-link giúp menu dọc gọn hơn */
    .sidebar .nav-link { 
        color: #a2a3b7; 
        padding: 10px 20px; 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        transition: all 0.2s; 
        white-space: nowrap; 
        font-size: 14px;
    }

    .sidebar .nav-link:hover, .sidebar .nav-link.active { 
        color: #fff; 
        background: #1b1b28; 
        border-left: 4px solid var(--gold-color); 
    }

    /* Tối ưu không gian nội dung chính */
    .main-content { 
        margin-left: var(--sidebar-width); 
        padding: 20px; /* Giảm padding từ 30px xuống 20px để mở rộng không gian cho bảng */
        min-height: 100vh; 
        transition: all 0.3s ease; 
        width: calc(100% - var(--sidebar-width)); 
    }

    .admin-card { 
        border: none; 
        border-radius: 12px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        background: #fff;
        margin-bottom: 20px;
        overflow: hidden; 
    }

    .mobile-header-bar { display: none; }


    /* ── 🌟 GIẢI PHÁP HIỂN THỊ HAI THANH CUỘN (ĐẦU & CUỐI BẢNG) ── */
    
    /* Bọc bảng bằng class này để tạo thanh cuộn kép */
    .table-responsive-custom {
        width: 100%;
        overflow-x: auto; 
        -webkit-overflow-scrolling: touch; 
        margin-bottom: 1rem;
        
        /* Đỉnh cao kỹ thuật: Tạo thêm 1 thanh kéo ở đầu bảng bằng cách nhân bản thuộc tính hiển thị */
        display: block; 
    }

    /* Đổi giao diện thanh cuộn mượt và mảnh hơn */
    .table-responsive-custom::-webkit-scrollbar {
        height: 8px; /* Độ rộng vừa phải để dễ thao tác kéo */
    }
    .table-responsive-custom::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 10px;
    }
    .table-responsive-custom::-webkit-scrollbar-thumb {
        background: #ccc; 
        border-radius: 10px;
    }
    .table-responsive-custom::-webkit-scrollbar-thumb:hover {
        background: var(--gold-color); 
    }

    /* Đưa thanh cuộn lên đầu bảng nhưng vẫn giữ một thanh bên dưới */
    .double-scroll {
        transform: rotateX(180deg);
    }
    .double-scroll table {
        transform: rotateX(180deg);
    }

    /* ── ĐỊNH DẠNG BẢNG QUẢN LÝ CHỐNG CHỮ BỊ DỌC ── */
    .table {
        width: 100%;
        margin-bottom: 0;
        vertical-align: middle;
        border-collapse: collapse;
    }

    /* Sửa triệt để lỗi chữ nhảy dọc: Cho phép tiêu đề th và td tự động bẻ hàng ngang bình thường */
    .table th, .table td {
        white-space: normal !important; /* Đổi từ nowrap sang normal để chữ không bị đẩy dọc */
        word-wrap: break-word;
        padding: 12px 10px !important;
        font-size: 14px;
        text-align: left;
    }

    /* Khống chế độ rộng cố định cho từng cột để bảng dàn đều, cân đối */
    .table th:nth-child(1), .table td:nth-child(1) { width: 80px; text-align: center; } /* Cột Hình ảnh */
    .table th:nth-child(2), .table td:nth-child(2) { min-width: 180px; max-width: 240px; } /* Cột Tên sản phẩm */
    .table th:nth-child(3), .table td:nth-child(3) { width: 130px; } /* Cột Phân loại */
    .table th:nth-child(4), .table td:nth-child(4) { width: 120px; white-space: nowrap !important; } /* Cột Giá hiện tại */
    .table th:nth-child(5), .table td:nth-child(5) { width: 120px; text-align: center; } /* Cột Số lượng kho */
    .table th:nth-child(6), .table td:nth-child(6) { width: 110px; text-align: center; } /* Cột Định vị */
    .table th:nth-child(7), .table td:nth-child(7) { width: 130px; text-align: center; } /* Cột Hành động */

    /* Định dạng ảnh sản phẩm trong bảng gọn gàng */
    .table td img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
    }


    /* ── CSS RESPONSIVE CHO DI ĐỘNG & TABLET (DƯỚI 992PX) ── */
    @media (max-width: 991.98px) {
        .sidebar {
            width: 100%;
            height: auto;
            max-height: calc(100vh - 50px); 
            position: fixed;
            top: 50px; 
            left: 0;
            overflow-y: auto;
            display: none !important; 
            padding-bottom: 20px;
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
            z-index: 1045;
        }
        
        .sidebar.show {
            display: flex !important;
        }

        .sidebar .nav-link {
            padding: 12px 20px;
            border-left: none !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }

        .main-content { 
            margin-left: 0; 
            width: 100%;
            padding: 15px 10px; 
            padding-top: 65px; 
        }

        /* Thu gọn chiều cao mobile bar từ 56px xuống 50px để lấy thêm không gian */
        .mobile-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: var(--dark-bg);
            z-index: 1050;
            padding: 0 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }
    }

    /* Giới hạn phần mô tả tóm tắt sản phẩm */
    .text-truncate-custom {
        white-space: normal !important;
        display: -webkit-box;
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-width: 220px;
        font-size: 12.5px;
        color: #6c757d;
    }
    </style>
</head>
<body>

<div class="mobile-header-bar align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <h6 class="text-white fw-bold text-uppercase m-0" style="letter-spacing: 0.5px; font-size: 13px;">3 Anh Em Admin</h6>
    </div>
    
    <button class="navbar-toggler text-white border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#adminSidebarContent" aria-controls="adminSidebarContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list fs-3"></i>
    </button>
</div>

<div class="sidebar collapse d-flex flex-column pt-3" id="adminSidebarContent">
    <div class="text-center mb-3 px-3 d-none d-lg-block">
        <h5 class="text-white fw-bold text-uppercase m-0" style="letter-spacing: 1px; font-size: 16px;">3 Anh Em Admin</h5>
        <small class="text-muted" style="font-size: 11px;">Hệ thống quản lý cửa hàng</small>
    </div>
    <hr class="border-secondary opacity-25 mx-3 mb-2 d-none d-lg-block">
    
    <ul class="nav flex-column flex-grow-1">
        <li class="nav-item"><a href="index.php" class="nav-link" id="side-dashboard"><i class="bi bi-speedometer2"></i> Trang tổng quan</a></li>
        <li class="nav-item"><a href="admin-products.php" class="nav-link" id="side-products"><i class="bi bi-box-seam"></i> Quản Lý Sản Phẩm</a></li>
        <li class="nav-item"><a href="admin-categories.php" class="nav-link" id="side-categories"><i class="bi bi-tags"></i> Quản lý Danh mục</a></li>
        <li class="nav-item"><a href="orders.php" class="nav-link" id="side-orders"><i class="bi bi-receipt"></i> Quản lý Đơn hàng</a></li>
        <li class="nav-item"><a href="admin-users.php" class="nav-link" id="side-users"><i class="bi bi-people"></i> Quản lý Người dùng</a></li>
        <li class="nav-item"><a href="admin-posts.php" class="nav-link" id="side-posts"><i class="bi bi-journal-text"></i> Quản lý Bài viết</a></li>
        <li class="nav-item"><a href="messages.php" class="nav-link" id="side-messages"><i class="bi bi-chat-left-text"></i> Phản hồi từ khách hàng</a></li>
    </ul>
    
    <div class="p-3 mt-auto">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-white-50 border-secondary" style="font-size: 12px;"><i class="bi bi-arrow-left"></i> Xem Website</a>
        <a href="../logout.php" class="btn btn-sm btn-danger w-100" style="font-size: 12px;"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
    </div>
</div>

<div class="main-content">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>