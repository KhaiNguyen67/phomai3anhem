<?php
// ============================================================
// File: contact.php
// Chức năng: Giao diện gửi liên hệ dành cho khách hàng
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';
?>

<div class="container my-5 pt-5">
    <div class="row g-5">
        <div class="col-lg-5">
            <div class="contact-info-card p-4 rounded-4 shadow-sm bg-white h-100">
                <h3 class="fw-bold text-dark mb-4">Thông Tin Liên Hệ</h3>
                <p class="text-muted mb-4">Hãy để lại lời nhắn cho chúng tôi nếu bạn có bất kỳ thắc mắc nào về sản phẩm hoặc chính sách đại lý.</p>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-geo-alt-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Địa chỉ cửa hàng</h6>
                        <small class="text-muted">Quận 12, Thành phố Hồ Chí Minh</small>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-envelope-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Email hỗ trợ</h6>
                        <small class="text-muted">khai67@gmail.com</small>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-telephone-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Hotline tư vấn</h6>
                        <small class="text-muted">0901234567</small>
                    </div>
                </div>

                <div class="mt-4 rounded-3 overflow-hidden border" style="height: 220px;">
                    <iframe src="//www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31347.26262272334!2d106.63389828969493!3d10.856553507187122!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317529798a06bc69%3A0xc1c961c2fe6bde91!2sIMC!5e0!3m2!1svi!2s!4v1779932525840!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>" 
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <h3 class="fw-bold text-dark mb-2">Gửi Lời Nhắn Cho Chúng Tôi</h3>
                <p class="text-muted mb-4 small">Chúng tôi thường phản hồi lại các yêu cầu trong vòng 24 giờ làm việc.</p>

                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] == 'success'): ?>
                        <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>Lời nhắn của bạn đã được gửi thành công! Cảm ơn bạn đã liên hệ.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif ($_GET['status'] == 'error'): ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 small" role="alert">
                            <i class="bi bi-x-circle-fill me-2"></i>Có lỗi xảy ra trong quá trình gửi, vui lòng thử lại!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="contact-process.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3" name="fullname" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control rounded-3" name="phone" required placeholder="0901234567">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Địa chỉ Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control rounded-3" name="email" required placeholder="example@gmail.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Chủ đề liên hệ</label>
                            <input type="text" class="form-control rounded-3" name="subject" placeholder="Hỏi về giá sỉ / Đóng góp ý kiến...">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Nội dung tin nhắn <span class="text-danger">*</span></label>
                            <textarea class="form-control rounded-3" name="message" rows="5" required placeholder="Nhập lời nhắn chi tiết tại đây..."></textarea>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-gold text-white rounded-pill px-5 py-2.5 fw-bold">
                                <i class="bi bi-send-fill me-2"></i>Gửi Lời Nhắn
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.btn-gold {
    background-color: #E5A93B;
    border: none;
    transition: all 0.3s ease;
}
.btn-gold:hover {
    background-color: #C98F2A;
    transform: translateY(-2px);
}
.icon-box {
    background-color: rgba(229, 169, 59, 0.15) !important;
    color: #E5A93B !important;
}
</style>

<?php include_once 'includes/footer.php'; ?>