<?php
// ============================================================
// File: login.php
// Chức năng: Đăng nhập hệ thống, phân quyền & Kiểm tra kích hoạt Email
// ============================================================
include_once 'config/db.php';

// BẮT ĐẦU XỬ LÝ LOGIC PHP TRƯỚC KHI XUẤT HTML
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = '';

// HỨNG THÔNG BÁO LỖI CHUYỂN HƯỚNG BẢO MẬT TỪ CHECKOUT
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Xóa ngay để tránh thông báo lặp lại khi người dùng tải lại trang (F5)
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ tài khoản và mật khẩu.';
    } else {
        // Tìm kiếm thông tin người dùng dựa vào Email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Nếu tài khoản tồn tại, tiến hành xác thực mật khẩu băm
        if ($user && password_verify($password, $user['password'])) {
            
            // KIỂM TRA TRẠNG THÁI KÍCH HOẠT TÀI KHOẢN
            if (isset($user['status']) && $user['status'] === 'inactive') {
                $error = 'Tài khoản của bạn chưa được kích hoạt. Vui lòng kiểm tra Email đăng ký để xác thực tài khoản.';
            } else {
                // Lưu giữ trạng thái đăng nhập vào Session khi trạng thái hợp lệ (active)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role']; // customer hoặc admin

                // Điều hướng chuẩn xác không lo lỗi trùng lặp headers
                if ($user['role'] === 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: index.php'); 
                }
                exit();
            }
        } else {
            $error = 'Tài khoản hoặc mật khẩu không chính xác.';
        }
    }
}

// SAU KHI LOGIC CHUYỂN HƯỚNG HOÀN TẤT, MỚI NHÚNG HEADER HTML XUỐNG ĐÂY
include_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center py-4">
        <div class="col-md-5" data-aos="zoom-in">
            <div class="glass-card p-5">
                <h2 class="fw-bold text-center mb-4">Đăng Nhập</h2>

                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger small py-2 fw-semibold text-center">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Địa chỉ Email</label>
                        <input type="email" name="email" class="form-control rounded-pill px-3" required placeholder="your-email@gmail.com">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Mật khẩu</label>
                        <input type="password" name="password" class="form-control rounded-pill px-3" required placeholder="••••••••">
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-gold py-2">Đăng Nhập</button>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center small mt-3">
                        <div class="text-muted">
                            Chưa có tài khoản? <a href="register.php" class="text-warning fw-bold text-decoration-none">Đăng ký ngay</a>
                        </div>
                        <div>
                            <a href="forgot-password.php" class="text-decoration-none text-muted">Quên mật khẩu?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>