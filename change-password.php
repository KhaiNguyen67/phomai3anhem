<?php
// ============================================================
// File: change-password.php
// Chức năng: Cho phép thành viên tự thay đổi mật khẩu cá nhân
// ============================================================
include_once 'config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Chặn nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Vui lòng điền đầy đủ tất cả các trường.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Mật khẩu mới và xác nhận mật khẩu không khớp nhau.';
    } elseif (strlen($new_password) < 6) {
        $error = 'Mật khẩu mới phải có độ dài tối thiểu từ 6 ký tự.';
    } else {
        // Lấy thông tin mật khẩu hiện tại trong DB
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if ($user && password_verify($old_password, $user['password'])) {
            // Mã hóa mật khẩu mới và cập nhật vào bảng dữ liệu
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            
            if ($stmt_update->execute([$hashed_password, $_SESSION['user_id']])) {
                $success = 'Thay đổi mật khẩu thành công!';
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại sau.';
            }
        } else {
            $error = 'Mật khẩu hiện tại không chính xác.';
        }
    }
}

include_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5" data-aos="zoom-in">
            <div class="glass-card p-5">
                <h3 class="fw-bold text-center mb-4">Đổi Mật Khẩu</h3>

                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger small py-2"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if(!empty($success)): ?>
                    <div class="alert alert-success small py-2"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="change-password.php">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mật khẩu hiện tại</label>
                        <input type="password" name="old_password" class="form-control rounded-pill px-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control rounded-pill px-3" required placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Xác nhận mật khẩu mới</label>
                        <input type="password" name="confirm_password" class="form-control rounded-pill px-3" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-gold py-2">Cập Nhật Mật Khẩu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>