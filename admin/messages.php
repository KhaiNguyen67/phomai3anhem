<?php
// ============================================================
// File: admin/messages.php
// Chức năng: Xem danh sách và CHI TIẾT lời nhắn từ khách hàng gửi qua trang liên hệ
// ============================================================
include_once '../config/db.php';
include_once 'includes/header.php';

// Bao gồm file kiểm tra quyền admin nếu có (Ví dụ: include_once 'admin-check.php';)

// Xử lý xóa lời nhắn nếu có yêu cầu
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt_del = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt_del->execute([$id]);
    
    // THAY THẾ CÂU LỆNH HEADER CŨ BẰNG JAVASCRIPT:
    echo "<script>window.location.href = 'messages.php?msg=success';</script>";
    exit();
}

// Lấy danh sách lời nhắn mới nhất
$stmt_msg = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
$messages = $stmt_msg->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Lời Nhắn - Phô Mai 3 Anh Em</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="mb-4">
        <a href="index.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại Dashboard
        </a>
    </div>

    <div class="glass-card p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">Hộp Thư Góp Ý & Liên Hệ</h3>
                <small class="text-muted">Danh sách lời nhắn từ biểu mẫu trang liên hệ của khách hàng</small>
            </div>
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                Tổng số: <?php echo count($messages); ?> lời nhắn
            </span>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Đã xóa lời nhắn thành công khỏi hệ thống!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white rounded-3 overflow-hidden">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 5%">STT</th>
                        <th style="width: 20%">Khách Hàng</th>
                        <th style="width: 20%">Thông Tin Liên Hệ</th>
                        <th style="width: 35%">Nội Dung Lời Nhắn</th>
                        <th style="width: 20%" class="text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($messages) > 0): ?>
                        <?php $stt = 1; foreach($messages as $msg): ?>
                            <tr>
                                <td><strong class="text-muted"><?php echo $stt++; ?></strong></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($msg['fullname']); ?></div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="small"><i class="bi bi-envelope me-1 text-secondary"></i><?php echo htmlspecialchars($msg['email']); ?></div>
                                    <div class="small"><i class="bi bi-telephone me-1 text-secondary"></i><?php echo htmlspecialchars($msg['phone'] ?? 'Không có'); ?></div>
                                </td>
                                <td>
                                    <div class="text-secondary small text-truncate-2">
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info text-white me-1 view-msg-btn" 
                                            title="Xem chi tiết lời nhắn"
                                            data-fullname="<?php echo htmlspecialchars($msg['fullname']); ?>"
                                            data-email="<?php echo htmlspecialchars($msg['email']); ?>"
                                            data-phone="<?php echo htmlspecialchars($msg['phone'] ?? 'Không có'); ?>"
                                            data-time="<?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?>"
                                            data-message="<?php echo htmlspecialchars($msg['message']); ?>">
                                        <i class="bi bi-eye-fill"></i> Chi tiết
                                    </button>
                                    
                                    <a href="mailto:<?php echo $msg['email']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Phản hồi qua Email">
                                        <i class="bi bi-reply-fill"></i>
                                    </a>
                                    
                                    <a href="messages.php?action=delete&id=<?php echo $msg['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa lời nhắn này không?');" 
                                       title="Xóa lời nhắn">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Hiện tại chưa có lời nhắn nào từ khách hàng.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="messageDetailModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-dark text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="bi bi-envelope-open-fill me-2 text-warning"></i>Chi Tiết Lời Nhắn</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 border-bottom pb-2">
                    <label class="text-muted small d-block">Khách hàng gửi:</label>
                    <strong id="modalFullname" class="text-dark fs-5"></strong>
                    <span id="modalTime" class="text-muted small d-block mt-1"></span>
                </div>
                <div class="row g-2 mb-3 bg-light p-2 rounded">
                    <div class="col-6">
                        <label class="text-muted small d-block"><i class="bi bi-envelope me-1"></i>Email:</label>
                        <span id="modalEmail" class="text-dark fw-medium small"></span>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small d-block"><i class="bi bi-telephone me-1"></i>Số điện thoại:</label>
                        <span id="modalPhone" class="text-dark fw-medium small"></span>
                    </div>
                </div>
                <div class="mb-1">
                    <label class="text-muted small d-block mb-1"><i class="bi bi-chat-left-text me-1 text-secondary"></i>Nội dung tin nhắn:</label>
                    <div id="modalContent" class="p-3 bg-white border rounded text-secondary shadow-sm-inner" style="white-space: pre-line; line-height: 1.6; max-height: 250px; overflow-y: auto; text-align: justify;">
                        </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a id="modalReplyBtn" href="#" class="btn btn-primary btn-sm rounded-pill px-3"><i class="bi bi-reply-fill me-1"></i>Phản hồi Email</a>
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Đoạn Script xử lý đổ dữ liệu động vào Modal khi bấm nút "Chi tiết"
document.addEventListener("DOMContentLoaded", function () {
    const viewButtons = document.querySelectorAll(".view-msg-btn");
    const myModal = new bootstrap.Modal(document.getElementById('messageDetailModal'));

    viewButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Lấy dữ liệu từ các thuộc tính data-attr của nút được bấm
            const fullname = this.getAttribute("data-fullname");
            const email = this.getAttribute("data-email");
            const phone = this.getAttribute("data-phone");
            const time = this.getAttribute("data-time");
            const message = this.getAttribute("data-message");

            // Gán dữ liệu vào các thẻ HTML tương ứng bên trong cấu trúc Modal
            document.getElementById("modalFullname").innerText = fullname;
            document.getElementById("modalTime").innerHTML = '<i class="bi bi-clock me-1"></i> Gửi lúc: ' + time;
            document.getElementById("modalEmail").innerText = email;
            document.getElementById("modalPhone").innerText = phone;
            document.getElementById("modalContent").innerText = message;
            
            // Thiết lập link phản hồi nhanh qua email
            document.getElementById("modalReplyBtn").setAttribute("href", "mailto:" + email);

            // Kích hoạt hiển thị Modal lên màn hình
            myModal.show();
        });
    });
});
</script>
</body>
</html>