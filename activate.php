<?php
// ============================================================
// File: activate.php
// Chức năng: Đón nhận Token và chính thức kích hoạt tài khoản
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

$message = '';
$status_class = 'alert-danger';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    // Kiểm tra xem mã token này có tồn tại ứng với tài khoản chưa kích hoạt nào không
    $stmt = $pdo->prepare("SELECT id FROM users WHERE activation_token = ? AND status = 'inactive'");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Cập nhật trạng thái thành 'active' và xóa token đi để bảo mật
        $update = $pdo->prepare("UPDATE users SET status = 'active', activation_token = NULL WHERE id = ?");
        if ($update->execute([$user['id']])) {
            $message = '🎉 Chúc mừng! Tài khoản của bạn đã được kích hoạt thành công. Bây giờ bạn đã có thể tiến hành đăng nhập và mua sắm.';
            $status_class = 'alert-success';
        } else {
            $message = 'Có lỗi xảy ra trong quá trình kích hoạt hệ thống, vui lòng thử lại sau.';
        }
    } else {
        $message = 'Mã xác thực không hợp lệ hoặc tài khoản này đã được kích hoạt trước đó.';
    }
} else {
    $message = 'Yêu cầu không hợp lệ. Thiếu mã Token xác thực kích hoạt.';
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6" data-aos="zoom-in">
            <div class="glass-card p-5 text-center">
                <h3 class="fw-bold mb-4">Trạng Thái Kích Hoạt</h3>
                
                <div class="alert <?php echo $status_class; ?> fw-semibold py-3 mb-4">
                    <?php echo $message; ?>
                </div>

                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-gold py-2">Đi Đến Đăng Nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>