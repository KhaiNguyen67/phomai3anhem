<?php
// ============================================================
// File: register.php
// Chức năng: Đăng ký tài khoản (Bắt buộc xác thực kích hoạt qua Email)
// ============================================================
include_once 'config/db.php';
include_once 'includes/mailer.php'; 
include_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; 
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Kiểm tra xem có trường bắt buộc nào bỏ trống không
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng điền đầy đủ các trường bắt buộc (*).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Định dạng Email không hợp lệ.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không trùng khớp. Vui lòng nhập lại.';
    } else {
        // Kiểm tra xem email đã có người sử dụng chưa
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email này đã được đăng ký sử dụng trên hệ thống.';
        } else {
            // Mã hóa mật khẩu bảo mật cao (Bcrypt)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // 1. Tạo chuỗi mã Token xác thực ngẫu nhiên, an toàn
            $activation_token = bin2hex(random_bytes(32));
            
            // 2. Chèn tài khoản vào database với trạng thái mặc định là 'inactive'
            $sql = "INSERT INTO users (full_name, email, password, phone, address, role, status, activation_token) 
                    VALUES (?, ?, ?, ?, ?, 'customer', 'inactive', ?)";
            $stmt_insert = $pdo->prepare($sql);
            
            if ($stmt_insert->execute([$full_name, $email, $hashed_password, $phone, $address, $activation_token])) {
                
                // 3. Đường dẫn liên kết để kích hoạt tài khoản
                $activate_link = "http://localhost/phomai3anhem/activate.php?token=" . $activation_token;
                
                // 4. Biên soạn nội dung Email xác nhận
                $mail_subject = "🔔 Xác thực kích hoạt tài khoản - Tiệm Phô Mai Cao Cấp";
                
                $mail_body = "
                <div style='background-color: #f8f9fa; padding: 40px; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; color: #333;'>
                    <div style='max-width: 520px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 25px rgba(0,0,0,0.05);'>
                        <div style='text-align: center; margin-bottom: 25px;'>
                            <h2 style='color: #d4af37; font-weight: bold; margin: 0;'>TIỆM PHÔ MAI CAO CẤP</h2>
                            <p style='color: #777; font-size: 14px; margin-top: 5px;'>Xác thực thông tin đăng ký thành viên</p>
                        </div>
                        <hr style='border: 0; border-top: 1px solid #eee; margin-bottom: 30px;'>
                        
                        <p>Xin chào <strong>{$full_name}</strong>,</p>
                        <p>Hệ thống nhận được yêu cầu đăng ký tài khoản thành viên mới bằng địa chỉ email này.</p>
                        <p>Để hoàn tất thủ tục và kích hoạt quyền đăng nhập trên hệ thống, vui lòng nhấn vào nút xác nhận bên dưới:</p>
                        
                        <div style='text-align: center; margin: 35px 0;'>
                            <a href='{$activate_link}' style='background-color: #d4af37; color: #ffffff; padding: 14px 35px; text-decoration: none; font-weight: bold; border-radius: 30px; display: inline-block; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3); letter-spacing: 0.5px;'>Kích Hoạt Tài Khoản</a>
                        </div>
                        
                        <div style='background: #fdf2f2; border-left: 4px solid #dc3545; padding: 15px; margin: 25px 0; border-radius: 6px;'>
                            <p style='margin: 0 0 5px 0; font-weight: bold; color: #b91c1c;'>⚠️ Bạn không thực hiện yêu cầu này?</p>
                            <p style='margin: 0; font-size: 13px; color: #555;'>Nếu bạn không đăng ký tài khoản tại hệ thống của chúng tôi, vui lòng bỏ qua email này. Tài khoản tạm thời này sẽ giữ trạng thái vô hiệu hóa và tự động bị hủy để bảo mật thông tin cho bạn.</p>
                        </div>
                        
                        <hr style='border: 0; border-top: 1px solid #eee; margin-top: 30px; margin-bottom: 20px;'>
                        <p style='font-size: 11px; color: #aaa; text-align: center; margin: 0;'>Đây là email tự động từ hệ thống, vui lòng không phản hồi lại thư này.</p>
                    </div>
                </div>
                ";
                
                // Thực thi gửi email ẩn dưới background
                sendSystemMail($email, $mail_subject, $mail_body);
                
                // Hiển thị thông báo hướng dẫn người dùng
                $success = 'Đăng ký bước đầu thành công! Một email xác thực đã được gửi đi. Vui lòng kiểm tra hộp thư (kiểm tra cả mục thư rác nếu không thấy) và bấm vào đường liên kết để chính thức kích hoạt tài khoản của bạn.';
                
                // Xóa sạch dữ liệu cũ trong các ô input sau khi xử lý thành công
                $full_name = $email = $phone = $address = '';
            } else {
                $error = 'Có lỗi xảy ra trong quá trình đăng ký, vui lòng thử lại.';
            }
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6" data-aos="zoom-in">
            <div class="glass-card p-5">
                <h2 class="fw-bold text-center mb-4">Đăng Ký Thành Viên</h2>
                
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger small py-2 fw-semibold text-center">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if(!empty($success)): ?>
                    <div class="alert alert-success small py-2 fw-semibold text-center">
                        <i class="bi bi-check-circle-fill me-1"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Họ và Tên *</label>
                        <input type="text" name="full_name" class="form-control rounded-pill px-3" required placeholder="Nguyễn Văn A" value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa chỉ Email *</label>
                        <input type="email" name="email" class="form-control rounded-pill px-3" required placeholder="name@example.com" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mật khẩu *</label>
                        <input type="password" name="password" class="form-control rounded-pill px-3" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Xác nhận mật khẩu *</label>
                        <input type="password" name="confirm_password" class="form-control rounded-pill px-3" required minlength="6" placeholder="Nhập lại mật khẩu chính xác">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control rounded-pill px-3" placeholder="0901234567" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Địa chỉ giao hàng</label>
                        <textarea name="address" class="form-control rounded-3" rows="2" placeholder="Số nhà, tên đường, quận/huyện..."><?php echo isset($address) ? htmlspecialchars($address) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-gold py-2">Tạo Tài Khoản</button>
                    </div>
                    <div class="text-center small text-muted">
                        Đã có tài khoản? <a href="login.php" class="text-warning fw-bold text-decoration-none">Đăng nhập tại đây</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>