<?php
// ============================================================
// File: config/db.php
// Chức năng: Kết nối CSDL MySQL bằng PDO (Đã sửa đổi Port 2333)
// ============================================================

$host = '127.0.0.1:3306';    // BẮT BUỘC: Thêm dấu hai chấm và số cổng 2333 vào đây
$dbname = 'phomai3anhem';   // Tên database của bạn
$username = 'root';         // Tên đăng nhập MySQL
$password = '';             // Mật khẩu trống theo cấu hình hệ thống của bạn

try {
    // Khởi tạo chuỗi kết nối PDO với thông số Host đã bao gồm Port 2333
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    
    $pdo = new PDO($dsn, $username, $password);
    
    // Cấu hình PDO bảo mật và tối ưu
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Nếu kết nối thất bại, dừng chương trình và in ra lỗi
    die("Lỗi kết nối Cơ sở dữ liệu: " . $e->getMessage());
}
?>
