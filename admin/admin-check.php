<?php
// Kích hoạt Session nếu chưa được bật ở các file trước đó
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra điều kiện bảo mật
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    
    // ĐÃ SỬA: Thêm "Location:" và đổi thành 2 dấu chấm "../" để lùi về thư mục gốc
    header("Location: ../index.php"); 
    exit(); // Chặn đứng không cho thực thi bất kỳ dòng code giao diện admin nào phía dưới
}
?>