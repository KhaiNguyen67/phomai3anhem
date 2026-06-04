<?php
// process_payment.php

if (isset($_POST['btn_thanh_toan'])) {
    $ma_don_hang = $_POST['ma_don_hang'];
    
    // 1. Kiểm tra xem người dùng đã chọn file chưa và không có lỗi
    if (isset($_FILES['bill_image']) && $_FILES['bill_image']['error'] == 0) {
        
        $file_name = $_FILES['bill_image']['name'];
        $file_tmp = $_FILES['bill_image']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Các đuôi file ảnh được phép dùng
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        
        if (in_array($file_ext, $allowed_extensions)) {
            
            // Đổi tên file thành mã đơn hàng để tránh trùng lặp và dễ quản lý (Ví dụ: DH123456_1717654321.jpg)
            $new_file_name = $ma_don_hang . "_" . time() . "." . $file_ext;
            
            // Đường dẫn thư mục lưu file trên host
            $upload_dir = "assets/img/proofs/";
            $target_file = $upload_dir . $new_file_name;
            
            // Di chuyển file từ thư mục tạm lên thư mục chính thức trên host
            if (move_uploaded_file($file_tmp, $target_file)) {
                
                /* 2. KẾT NỐI DATABASE VÀ CẬP NHẬT TRẠNG THÁI TẠI ĐÂY (Ví dụ minh họa)
                   
                   $sql = "UPDATE don_hang SET 
                            trang_thai = 'Cho_Duyet', 
                            anh_minh_chung = '$target_file' 
                           WHERE ma_don_hang = '$ma_don_hang'";
                   mysqli_query($conn, $sql);
                */
                
                // Hiển thị thông báo thành công cho khách
                echo "<script>
                    alert('Hệ thống đã nhận được minh chứng! Vui lòng chờ Admin kiểm tra và duyệt đơn hàng trong ít phút.');
                    window.location.href = 'index.html'; // Chuyển hướng về trang chủ
                </script>";
                
            } else {
                echo "<script>alert('Có lỗi xảy ra trong quá trình tải ảnh lên host. Vui lòng thử lại.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Định dạng file không hợp lệ! Vui lòng chỉ tải lên file ảnh (jpg, png, jpeg).'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Vui lòng chọn ảnh chụp biên lai thanh toán.'); window.history.back();</script>";
    }
} else {
    // Nếu truy cập lén vào file này không qua submit form thì đá về checkout
    header("Location: checkout.php");
    exit();
}
?>