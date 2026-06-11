<?php
// ============================================================
// File: logout.php
// Chức năng: Đăng xuất, hủy session và quay về trang chủ
// ============================================================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Xóa toàn bộ dữ liệu trong session
$_SESSION = array();

// Hủy bỏ session hoàn toàn
session_destroy();

// Quay về trang chủ ngay lập tức
header("Location: index.php");
exit();