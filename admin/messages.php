<?php
// ============================================================
// File: admin/messages.php
// Chức năng: Xem danh sách, CHI TIẾT và PHẢN HỒI lời nhắn từ khách hàng bằng PHPMailer
// ============================================================
include_once '../config/db.php';
include_once 'admin-check.php';
include_once 'includes/header.php';

// Gọi thư viện PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../includes/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../includes/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../includes/PHPMailer/src/SMTP.php';

// --------------------------------------------------------
// XỬ LÝ 1: GỬI MAIL PHẢN HỒI QUA POST FORM STANDARD
// --------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply_message') {
    $msg_id = intval($_POST['id']);
    $customer_email = trim($_POST['email']);
    $customer_name = trim($_POST['fullname']);
    $reply_content = trim($_POST['reply_content']);

    if (!empty($customer_email) && !empty($reply_content)) {
        $mail = new PHPMailer(true);
        try {
            // Cấu hình Server SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'khaicc67@gmail.com'; 
            $mail->Password   = 'nxfq xhll heys yorx'; // Mật khẩu ứng dụng (App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // Người gửi & Người nhận
            $mail->setFrom('khaicc67@gmail.com', 'Phô Mai 3 Anh Em');
            $mail->addAddress($customer_email, $customer_name);

            // Nội dung Mail định dạng HTML
            $mail->isHTML(true);
            $mail->Subject = "Phản hồi thông tin liên hệ từ Cửa hàng Phô Mai 3 Anh Em";
            
            // Thiết kế template email cơ bản gửi đến khách hàng
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px;'>
                    <h3 style='color: #d97706; border-bottom: 2px solid #f59e0b; padding-bottom: 10px;'>Xin chào $customer_name,</h3>
                    <p>Cảm ơn bạn đã để lại lời nhắn/góp ý cho hệ thống cửa hàng <strong>Phô Mai 3 Anh Em</strong>.</p>
                    <div style='background-color: #f8fafc; border-left: 4px solid #cbd5e1; padding: 10px 15px; margin: 15px 0; color: #475569; font-style: italic;'>
                        Nội dung bạn gửi: <br> \" " . nl2br(htmlspecialchars($_POST['original_msg'])) . " \"
                    </div>
                    <p><strong>Cửa hàng xin được phản hồi đến bạn như sau:</strong></p>
                    <p style='line-height: 1.6; color: #1e293b; background-color: #fef3c7; padding: 15px; border-radius: 5px; border: 1px solid #fde68a;'>" . nl2br(htmlspecialchars($reply_content)) . "</p>
                    <p style='margin-top: 20px;'>Nếu có thêm bất kỳ thắc mắc nào, quý khách vui lòng liên hệ lại với chúng tôi qua số điện thoại cửa hàng.</p>
                    <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #94a3b8; text-align: center;'>Đây là email phản hồi từ Ban quản trị Website Phô Mai 3 Anh Em.</p>
                </div>
            ";

            $mail->send();

            // Cập nhật trạng thái 'status' trong bảng contacts
            try {
                $stmt_update = $pdo->prepare("UPDATE contacts SET status = 'replied' WHERE id = ?");
                $stmt_update->execute([$msg_id]);
            } catch (Exception $e) {
                // Bỏ qua nếu cột status tạm thời chưa khả dụng trong cơ sở dữ liệu
            }

            echo "<script>window.location.href = 'messages.php?msg=reply_success';</script>";
            exit();
        } catch (Exception $e) {
            echo "<script>window.location.href = 'messages.php?msg=reply_error&detail=" . urlencode($mail->ErrorInfo) . "';</script>";
            exit();
        }
    }
}

// --------------------------------------------------------
// XỬ LÝ 2: XÓA LỜI NHẮN
// --------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt_del = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt_del->execute([$id]);
    
    echo "<script>window.location.href = 'messages.php?msg=success';</script>";
    exit();
}

// Lấy danh sách lời nhắn mới nhất từ cơ sở dữ liệu
$stmt_msg = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
$messages = $stmt_msg->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Lời Nhắn - Phô Mai 3 Anh Em</title>
   
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
        
        /* 🎯 Ép cột Thao tác luôn nằm ngang tuyệt đối */
        .action-buttons-group {
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 6px; /* Khoảng cách đều giữa các nút bấm */
            white-space: nowrap !important; /* Tuyệt đối không rớt dòng */
        }
    </style>
</head>
<body>


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
                <i class="bi bi-check-circle-fill me-2"></i>Đã xóa lời nhắn thành công khỏi hệ thống!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'reply_success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-send-check-fill me-2"></i>Hệ thống đã gửi mail phản hồi tới khách hàng thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'reply_error'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Gửi mail thất bại! Lỗi phần cứng SMTP hoặc cấu hình sai.
                <br><small>Chi tiết: <?php echo htmlspecialchars($_GET['detail'] ?? ''); ?></small>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive table-responsive-custom double-scroll">
            <table class="table table-hover align-middle bg-white rounded-3 overflow-hidden">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 5%">STT</th>
                        <th style="width: 18%">Khách Hàng</th>
                        <th style="width: 22%">Thông Tin Liên Hệ</th>
                        <th style="width: 30%">Nội Dung Lời Nhắn</th>
                        <th style="width: 10%">Trạng Thái</th>
                        <th style="width: 15%" class="text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($messages) > 0): ?>
                        <?php $stt = 1; foreach($messages as $msg): 
                            $customerName = htmlspecialchars($msg['name'] ?? $msg['fullname'] ?? 'Ẩn danh');
                        ?>
                            <tr>
                                <td><strong class="text-muted"><?php echo $stt++; ?></strong></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $customerName; ?></div>
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
                                <td>
                                    <?php if(isset($msg['status']) && $msg['status'] === 'replied'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Đã phản hồi</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Chưa trả lời</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons-group">
                                        <button class="btn btn-sm btn-info text-white view-msg-btn" 
                                                title="Xem chi tiết lời nhắn"
                                                data-id="<?php echo $msg['id']; ?>"
                                                data-fullname="<?php echo $customerName; ?>"
                                                data-email="<?php echo htmlspecialchars($msg['email']); ?>"
                                                data-phone="<?php echo htmlspecialchars($msg['phone'] ?? 'Không có'); ?>"
                                                data-time="<?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?>"
                                                data-message="<?php echo htmlspecialchars($msg['message']); ?>">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        
                                        <button class="btn btn-sm btn-warning text-dark open-reply-btn" 
                                                title="Phản hồi bằng PHPMailer"
                                                data-id="<?php echo $msg['id']; ?>"
                                                data-fullname="<?php echo $customerName; ?>"
                                                data-email="<?php echo htmlspecialchars($msg['email']); ?>"
                                                data-message="<?php echo htmlspecialchars($msg['message']); ?>">
                                            <i class="bi bi-reply-fill"></i> <span class="d-none d-sm-inline">Phản hồi</span>
                                        </button>
                                        
                                        <a href="messages.php?action=delete&id=<?php echo $msg['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa lời nhắn này không?');" 
                                           title="Xóa lời nhắn">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Hiện tại chưa có lời nhắn nào từ khách hàng.</td>
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
                    <div id="modalContent" class="p-3 bg-white border rounded text-secondary" style="white-space: pre-line; line-height: 1.6; max-height: 250px; overflow-y: auto; text-align: justify;">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="replyMailModal" tabindex="-1" aria-labelledby="replyModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <form action="messages.php" method="POST">
                <input type="hidden" name="action" value="reply_message">
                <input type="hidden" name="id" id="replyId">
                <input type="hidden" name="original_msg" id="replyOriginalMsg">

                <div class="modal-header bg-warning text-dark" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h5 class="modal-title fw-bold" id="replyModalTitle">
                        <i class="bi bi-send-fill me-2"></i>Gửi Phản Hồi Trực Tiếp Tới Khách Hàng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Người nhận:</label>
                            <input type="text" class="form-control bg-light" id="replyFullname" name="fullname" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Email Khách hàng:</label>
                            <input type="email" class="form-control bg-light" id="replyEmail" name="email" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Nội dung soạn thảo phản hồi:</label>
                        <textarea class="form-control border-warning" name="reply_content" rows="6" 
                                  placeholder="Nhập nội dung thư trả lời khách hàng tại đây... Hệ thống tự động bọc giao diện thư chuyên nghiệp." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light-subtle">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold rounded-pill px-4">
                        <i class="bi bi-paper-plane-fill me-1"></i> Bắt đầu gửi Mail
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Xử lý mở Modal xem chi tiết tin nhắn
    const viewButtons = document.querySelectorAll(".view-msg-btn");
    const detailModal = new bootstrap.Modal(document.getElementById('messageDetailModal'));

    viewButtons.forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("modalFullname").innerText = this.getAttribute("data-fullname");
            document.getElementById("modalTime").innerHTML = '<i class="bi bi-clock me-1"></i> Gửi lúc: ' + this.getAttribute("data-time");
            document.getElementById("modalEmail").innerText = this.getAttribute("data-email");
            document.getElementById("modalPhone").innerText = this.getAttribute("data-phone");
            document.getElementById("modalContent").innerText = this.getAttribute("data-message");

            detailModal.show();
        });
    });

    // 2. Xử lý mở Modal viết mail phản hồi trực tiếp bằng PHPMailer
    const replyButtons = document.querySelectorAll(".open-reply-btn");
    const replyModal = new bootstrap.Modal(document.getElementById('replyMailModal'));

    replyButtons.forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("replyId").value = this.getAttribute("data-id");
            document.getElementById("replyFullname").value = this.getAttribute("data-fullname");
            document.getElementById("replyEmail").value = this.getAttribute("data-email");
            document.getElementById("replyOriginalMsg").value = this.getAttribute("data-message");

            replyModal.show();
        });
    });
});
</script>
</body>
</html>