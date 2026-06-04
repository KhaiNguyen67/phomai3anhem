<?php 
include_once '../config/db.php'; 

// 1. Xử lý khóa / mở khóa tài khoản thành viên nhanh bằng GET
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status') {
    $user_id = (int)$_GET['id'];
    $current_status = (int)$_GET['status'];
    $new_status = ($current_status === 1) ? 0 : 1; // Đảo ngược trạng thái hoạt động

    $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?")->execute([$new_status, $user_id]);
    header("Location: users.php");
    exit();
}

include_once 'includes/header.php'; 
// Đọc danh sách tất cả người dùng hệ thống (Theo schema cấu trúc database.sql)
$users = $pdo->query("SELECT * FROM users ORDER BY role ASC, id DESC")->fetchAll();
?>

<div class="mb-4">
    <h4 class="fw-bold text-dark"><i class="bi bi-people me-2"></i>Quản Lý Người Dùng & Thành Viên</h4>
</div>

<div class="card admin-card bg-white p-4" data-aos="fade-up">
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center m-0">
            <thead class="table-light">
                <tr>
                    <th>Mã số</th>
                    <th>Tên tài khoản</th>
                    <th>Địa chỉ Email</th>
                    <th>Vai trò (Role)</th>
                    <th>Ngày tham gia</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><strong>#<?php echo $u['id']; ?></strong></td>
                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($u['username']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <span class="badge <?php echo $u['role'] === 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                            <?php echo $u['role'] === 'admin' ? 'Quản trị viên' : 'Khách hàng'; ?>
                        </span>
                    </td>
                    <td><?php echo isset($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : date('d/m/Y'); ?></td>
                    <td>
                        <span class="badge <?php echo $u['is_active'] == 1 ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo $u['is_active'] == 1 ? 'Đang hoạt động' : 'Đang bị khóa'; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($u['role'] !== 'admin'): // Bảo vệ tài khoản admin tối cao không bị tự khóa ?>
                            <a href="users.php?action=toggle_status&id=<?php echo $u['id']; ?>&status=<?php echo $u['is_active']; ?>" 
                               class="btn btn-sm <?php echo $u['is_active'] == 1 ? 'btn-outline-warning' : 'btn-outline-success'; ?> px-3">
                                <i class="bi <?php echo $u['is_active'] == 1 ? 'bi-lock-fill' : 'bi-unlock-fill'; ?> me-1"></i>
                                <?php echo $u['is_active'] == 1 ? 'Khóa' : 'Mở khóa'; ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted small">Mặc định</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>