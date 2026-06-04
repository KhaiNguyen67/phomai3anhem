<?php
// ============================================================
// File: forgot-password.php
// ============================================================
include_once 'config/db.php';
include_once 'includes/mailer.php'; 
include_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_email'])) {
    $email = trim($_POST['email']);
    
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expire_time = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        $stmt_update = $pdo->prepare("UPDATE users SET reset_token = ?, token_expire = ? WHERE id = ?");
        $stmt_update->execute([$token, $expire_time, $user['id']]);
        
        // Đường dẫn link kích hoạt (Thay đổi 'your-project' cho khớp thư mục của bạn)
        $reset_link = "http://localhost/phomai3anhem/reset-password.php?token=" . $token;
        
        $subject = "🔒 Yêu cầu đặt lại mật khẩu tài khoản Phô Mai";
        $body = "
        <div style='background-color: #f8f9fa; padding: 40px; font-family: sans-serif; color: #333;'>
            <div style='max-width: 520px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 25px rgba(0,0,0,0.05);'>
                <h2 style='color: #d4af37; text-align: center; margin: 0;'>TIỆM PHÔ MAI CAO CẤP</h2>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                <p>Xin chào,</p>
                <p>Vui lòng nhấn vào nút xác nhận dưới đây để tiến hành thiết lập lại mật khẩu mới:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$reset_link}' style='background-color: #d4af37; color: #ffffff; padding: 12px 30px; text-decoration: none; font-weight: bold; border-radius: 30px; display: inline-block;'>Đặt Lại Mật Khẩu</a>
                </div>
                <p style='font-size: 13px; color: #ca0000;'>* Lưu ý: Đường dẫn này chỉ có hiệu lực sử dụng trong vòng 15 phút.</p>
            </div>
        </div>";
        
        if (sendSystemMail($email, $subject, $body)) {
            $success = 'Hệ thống đã gửi một liên kết xác nhận đến Email của bạn. Vui lòng kiểm tra hộp thư.';
        } else {
            $error = 'Có lỗi xảy ra trong quá trình gửi Mail. Vui lòng thử lại sau.';
        }
    } else {
        $error = 'Địa chỉ Email này không tồn tại trên hệ thống.';
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-card p-5">
                <h3 class="fw-bold text-center mb-4">Khôi Phục Mật Khẩu</h3>
                <?php if(!empty($error)): ?><div class="alert alert-danger small py-2"><?php echo $error; ?></div><?php endif; ?>
                <?php if(!empty($success)): ?><div class="alert alert-success small py-2"><?php echo $success; ?></div><?php endif; ?>

                <?php if(empty($success)): ?>
                    <form method="POST" action="forgot-password.php">
                        <p class="text-muted small text-center mb-4">Nhập Email tài khoản của bạn để nhận liên kết khôi phục mật khẩu.</p>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Địa chỉ Email đăng ký</label>
                            <input type="email" name="email" class="form-control rounded-pill px-3" required placeholder="name@example.com">
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="check_email" class="btn btn-gold py-2"><i class="bi bi-envelope-paper me-2"></i>Gửi Liên Kết Xác Thực</button>
                        </div>
                    </form>
                <?php endif; ?>
                <div class="text-center small text-muted mt-4"><a href="login.php" class="text-decoration-none text-warning fw-bold"><i class="bi bi-arrow-left"></i> Quay lại Đăng Nhập</a></div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'includes/footer.php'; ?>