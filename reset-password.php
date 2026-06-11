<?php
// ============================================================
// File: reset-password.php
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

$error = '';
$success = '';
$valid_token = false;
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (!empty($token)) {
    // Kiểm tra tính hợp lệ và thời gian hiệu lực của Token trong DB
    $current_time = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expire > ?");
    $stmt->execute([$token, $current_time]);
    $user = $stmt->fetch();
    
    if ($user) {
        $valid_token = true;
        
        // Xử lý đổi mật khẩu khi người dùng submit form
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            if (strlen($password) < 6) {
                $error = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            } elseif ($password !== $confirm_password) {
                $error = 'Mật khẩu xác nhận không trùng khớp.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Cập nhật mật khẩu mới và hủy bỏ Token để không thể dùng lại
                $stmt_update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expire = NULL WHERE id = ?");
                if ($stmt_update->execute([$hashed_password, $user['id']])) {
                    $success = 'Mật khẩu của bạn đã được thay đổi thành công! Bạn có thể đăng nhập ngay bây giờ.';
                    $valid_token = false; // Ẩn form nhập sau khi đổi thành công
                } else {
                    $error = 'Có lỗi xảy ra, vui lòng thử lại.';
                }
            }
        }
    } else {
        $error = 'Liên kết xác nhận đã hết hạn (quá 15 phút) hoặc không hợp lệ. Vui lòng thực hiện lại lệnh quên mật khẩu.';
    }
} else {
    $error = 'Yêu cầu không hợp lệ. Không tìm thấy mã xác thực.';
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-card p-5">
                <h3 class="fw-bold text-center mb-4">Đặt Lại Mật Khẩu</h3>
                <?php if(!empty($error)): ?><div class="alert alert-danger small py-2 text-center"><?php echo $error; ?></div><?php endif; ?>
                <?php if(!empty($success)): ?><div class="alert alert-success small py-2 text-center"><?php echo $success; ?></div><?php endif; ?>

                <?php if($valid_token): ?>
                    <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mật khẩu mới *</label>
                            <input type="password" name="password" class="form-control rounded-pill px-3" required placeholder="Tối thiểu 6 ký tự">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Xác nhận mật khẩu mới *</label>
                            <input type="password" name="confirm_password" class="form-control rounded-pill px-3" required placeholder="Nhập lại mật khẩu mới">
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="update_password" class="btn btn-gold py-2">Xác Nhận Đổi Mật Khẩu</button>
                        </div>
                    </form>
                <?php endif; ?>
                <div class="text-center small text-muted mt-4"><a href="login.php" class="text-decoration-none text-warning fw-bold">Quay lại Đăng Nhập</a></div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'includes/footer.php'; ?>