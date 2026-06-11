<?php
// ============================================================
// File: config/db.php
// ============================================================

$host = '127.0.0.1:3306';    //  Thêm dấu hai chấm và số cổng (hiện tại là 3306) vào đây
$dbname = 'phomai3anhem';   
$username = 'root';         
$password = '';             

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
