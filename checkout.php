<?php
// ============================================================
// File: checkout.php
// Chức năng: Trang tiến hành thanh toán (Đã tích hợp thêm cổng VNPAY)
// ============================================================
session_start();
include_once 'config/db.php'; 

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = "Cần đăng nhập mới mua hàng được.";
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        [
            'id' => 13,
            'name' => 'Emmental Grand Cru',
            'image' => 'emmental-grand-cru.jpg',
            'price' => 340000,
            'quantity' => 1
        ]
    ];
}

$user_logged = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_logged = $stmt->fetch();
}

$total_money = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_money += $item['price'] * $item['quantity'];
}

include_once 'includes/header.php'; 
?>

<div class="container my-5 pt-4">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark m-0">Tiến Hành Thanh Toán</h2>
        <p class="text-muted small">Vui lòng điền thông tin nhận hàng chính xác để thực hiện mua hàng.</p>
    </div>

    <div id="qrPaymentSection" class="row justify-content-center mb-5 d-none">
        <div class="col-12 col-md-6 text-center">
            <div class="card border-0 shadow-lg rounded-4 p-4 bg-white border-top border-4 border-warning">
                <div class="text-success mb-2">
                    <i class="bi bi-check-circle-fill display-5"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Đặt Hàng Thành Công!</h4>
                <p class="text-muted small">Mã đơn hàng của bạn: <span class="fw-bold text-danger" id="txtOrderCode">#0</span></p>
                <hr class="text-muted my-3">
                <p class="fw-bold text-secondary mb-3"><i class="bi bi-qr-code me-2"></i>QUÉT MÃ QR ĐỂ THANH TOÁN</p>
                
                <img src="https://img.vietqr.io/image/MBBank-09128390183-qr_only.png?amount=<?= $total_money ?>&addInfo=Phomai3Anhem%20ThanhToan" 
                     id="imgQrCode" alt="Mã QR Thanh Toán" class="img-fluid rounded-3 border p-2 bg-light shadow-sm mb-3" style="max-width: 240px;">
                
                <div class="alert alert-warning text-start small mb-3 rounded-3">
                    <strong>Hướng dẫn:</strong> Mở ứng dụng Ngân hàng quét mã trên để chuyển khoản số tiền <strong><?= number_format($total_money, 0, ',', '.') ?>đ</strong>.
                </div>

                <div class="card bg-light border border-dashed p-3 rounded-3 text-start mb-4">
                    <form id="formProofPayment" onsubmit="submitProofPayment(event)">
                        <input type="hidden" id="proofOrderId" name="order_id" value="">
                        <label for="billImage" class="form-label small fw-bold text-dark"><i class="bi bi-camera-fill me-1"></i> Tải lên ảnh chụp biên lai thành công *</label>
                        <input class="form-control form-control-sm rounded-3 mb-2" type="file" id="billImage" name="proof_image" accept="image/*" required>
                        <p class="text-danger m-0" style="font-size: 11px;">(*) Vui lòng gửi ảnh rõ mã giao dịch để hệ thống duyệt đơn.</p>
                        <button type="submit" id="btnSubmitProof" class="btn btn-gold text-white btn-sm w-100 mt-3 rounded-pill fw-bold text-uppercase py-2">
                            Xác nhận tôi đã chuyển khoản
                        </button>
                    </form>
                </div>
                <div>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-4">Quay về Trang Chủ</a>
                </div>
            </div>
        </div>
    </div>

    <div id="codPaymentSection" class="row justify-content-center mb-5 d-none">
        <div class="col-12 col-md-6 text-center">
            <div class="card border-0 shadow-lg rounded-4 p-5 bg-white border-top border-4 border-success">
                <div class="text-success mb-3">
                    <i class="bi bi-bag-check-fill display-4 text-success"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Xác Nhận Đơn Hàng Thành Công!</h4>
                <p class="text-muted small">Mã đơn hàng của bạn: <span class="fw-bold text-success" id="txtCodOrderCode">#0</span></p>
                <hr class="text-muted my-3">
                <div class="alert alert-success text-start small mb-4 rounded-3">
                    <i class="bi bi-info-circle-fill me-2"></i>Phương thức: <strong>Thanh toán tiền mặt khi nhận hàng (COD)</strong>.<br>
                    Số tiền cần chuẩn bị: <strong><?= number_format($total_money, 0, ',', '.') ?>đ</strong>.
                </div>
                <div>
                    <a href="index.php" class="btn btn-success text-white rounded-pill px-4 border-0" style="background-color: #198754;">Quay về Trang Chủ</a>
                </div>
            </div>
        </div>
    </div>

    <div id="checkoutFormSection" class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <form id="formCheckout" onsubmit="event.preventDefault();">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-fill text-warning me-2"></i>Thông Tin Người Mua</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Họ và tên người mua *</label>
                            <input type="text" class="form-control rounded-3" id="buyerName" required value="<?= $user_logged ? htmlspecialchars($user_logged['full_name']) : 'Tung' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Số điện thoại *</label>
                            <input type="tel" class="form-control rounded-3" id="buyerPhone" required value="<?= $user_logged ? htmlspecialchars($user_logged['phone']) : '09128390183' ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Địa chỉ Email</label>
                            <input type="email" class="form-control rounded-3" id="buyerEmail" value="<?= $user_logged ? htmlspecialchars($user_logged['email']) : 'Tung@gmail.com' ?>">
                        </div>
                    </div>

                    <hr class="text-muted my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark m-0"><i class="bi bi-truck text-warning me-2"></i>Thông Tin Nhận Hàng</h5>
                        <div class="form-check">
                            <input class="form-check-input border-warning" type="checkbox" id="chkSameAsBuyer" onchange="syncBuyerToReceiver()">
                            <label class="form-check-label small fw-bold text-warning cursor-pointer" for="chkSameAsBuyer">GIỐNG NGƯỜI MUA</label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Họ và tên người nhận *</label>
                            <input type="text" class="form-control rounded-3" id="receiverName" required placeholder="Tên người nhận hàng...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Số điện thoại nhận *</label>
                            <input type="tel" class="form-control rounded-3" id="receiverPhone" required placeholder="Số điện thoại gọi giao hàng...">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Địa chỉ giao hàng chính xác *</label>
                            <textarea class="form-control rounded-3" id="receiverAddress" rows="2" required placeholder="Số nhà, tên đường..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea class="form-control rounded-3" id="orderNotes" rows="2" placeholder="Ví dụ: Giao giờ hành chính..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bag-check-fill text-warning me-2"></i>Đơn Hàng</h5>
                    <div class="order-summary-list mb-3">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="assets/img/<?= htmlspecialchars($item['image']) ?>" class="rounded-3 border" style="width: 50px; height: 50px; object-fit: contain;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 small"><?= htmlspecialchars($item['name']) ?></h6>
                                        <small class="text-muted"><?= number_format($item['price'], 0, ',', '.') ?>đ x <?= $item['quantity'] ?></small>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark small"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between mb-2 small text-secondary">
                            <span>Tạm tính:</span>
                            <span><?= number_format($total_money, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-dark">Tổng thanh toán:</span>
                            <span class="fw-bold text-danger fs-5"><?= number_format($total_money, 0, ',', '.') ?>đ</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark mb-2">Chọn phương thức thanh toán</label>
                        
                       

                        <div class="form-check border rounded-3 p-3 mb-2 bg-light d-flex align-items-start gap-2 cur-pointer method-box" id="boxVnpay">
                            <input class="form-check-input mt-1 ms-0 border-secondary" type="radio" name="payment_method" id="radVnpay" value="vnpay">
                            <label class="form-check-label fw-bold text-dark small ms-1" for="radVnpay">
                                <i class="bi bi-credit-card text-primary me-1"></i> Thanh toán trực tuyến qua Cổng VNPAY
                            </label>
                        </div>

                        <div class="form-check border rounded-3 p-3 bg-light d-flex align-items-start gap-2 cur-pointer method-box" id="boxCod">
                            <input class="form-check-input mt-1 ms-0 border-secondary" type="radio" name="payment_method" id="radCod" value="cod">
                            <label class="form-check-label fw-bold text-dark small ms-1" for="radCod">
                                <i class="bi bi-cash-coin text-success me-1"></i> Thanh toán tiền mặt khi nhận hàng (COD)
                            </label>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="triggerSubmitForm()" class="btn btn-gold text-white w-100 py-2.5 rounded-pill fw-bold text-uppercase">
                    Xác Nhận Đặt Hàng & Thanh Toán
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.method-box').forEach(box => {
    box.addEventListener('click', () => {
        document.querySelectorAll('.method-box').forEach(b => b.classList.remove('active-method'));
        box.classList.add('active-method');
        box.querySelector('input[type="radio"]').checked = true;
    });
});

function syncBuyerToReceiver() {
    let isChecked = document.getElementById('chkSameAsBuyer').checked;
    if (isChecked) {
        document.getElementById('receiverName').value = document.getElementById('buyerName').value;
        document.getElementById('receiverPhone').value = document.getElementById('buyerPhone').value;
    } else {
        document.getElementById('receiverName').value = "";
        document.getElementById('receiverPhone').value = "";
    }
}

function triggerSubmitForm() {
    let form = document.getElementById('formCheckout');
    if (form.checkValidity()) {
        saveOrderToDatabase();
    } else {
        form.reportValidity();
    }
}

function saveOrderToDatabase() {
    let btn = document.querySelector('button[onclick="triggerSubmitForm()"]');
    let selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tạo đơn hàng...';

    let payload = {
        full_name: document.getElementById('buyerName').value,
        phone: document.getElementById('buyerPhone').value,
        email: document.getElementById('buyerEmail').value,
        note: document.getElementById('orderNotes').value,
        receiver_name: document.getElementById('receiverName').value,
        receiver_phone: document.getElementById('receiverPhone').value,
        receiver_address: document.getElementById('receiverAddress').value,
        payment_method: selectedMethod 
    };

    fetch('save-order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('checkoutFormSection').classList.add('d-none');

            if (selectedMethod === 'qr') {
                document.getElementById('txtOrderCode').innerText = '#' + data.order_id;
                document.getElementById('proofOrderId').value = data.order_id;
                let qrUrl = `https://img.vietqr.io/image/MBBank-09128390183-qr_only.png?amount=<?= $total_money ?>&addInfo=Phomai3Anhem%20DH${data.order_id}`;
                document.getElementById('imgQrCode').src = qrUrl;
                document.getElementById('qrPaymentSection').classList.remove('d-none');
            } 
            // ĐÃ TÍCH HỢP: Nếu chọn VNPAY thì tự động chuyển trang xử lý liên kết cổng
            else if (selectedMethod === 'vnpay') {
                window.location.href = 'process_payment.php?order_id=' + data.order_id;
            } 
            else {
                document.getElementById('txtCodOrderCode').innerText = '#' + data.order_id;
                document.getElementById('codPaymentSection').classList.remove('d-none');
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            alert('Lỗi: ' + data.message);
            btn.disabled = false;
            btn.innerText = 'Xác Nhận Đặt Hàng & Thanh Toán';
        }
    })
    .catch(err => {
        alert('Lỗi kết nối hệ thống.');
        btn.disabled = false;
        btn.innerText = 'Xác Nhận Đặt Hàng & Thanh Toán';
    });
}

function submitProofPayment(event) {
    event.preventDefault();
    let form = document.getElementById('formProofPayment');
    let formData = new FormData(form);
    let submitBtn = document.getElementById('btnSubmitProof');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang gửi ảnh...';

    fetch('process-proof.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Gửi ảnh thành công! Chờ Admin phê duyệt đơn hàng.');
            window.location.href = 'thankyou.php'; 
        } else {
            alert('Lỗi: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerText = 'Xác nhận tôi đã chuyển khoản';
        }
    })
    .catch(err => {
        alert('Có lỗi tải file.');
        submitBtn.disabled = false;
        submitBtn.innerText = 'Xác nhận tôi đã chuyển khoản';
    });
}
</script>

<style>
.btn-gold { background-color: #E5A93B; border: none; font-weight: 600; transition: all 0.3s; }
.btn-gold:hover { background-color: #C98F2A; transform: translateY(-1px); }
.cursor-pointer, .cur-pointer { cursor: pointer; }
.form-control:focus, .form-check-input:focus { border-color: #E5A93B !important; box-shadow: 0 0 0 0.25rem rgba(229, 169, 59, 0.15) !important; }
.active-method { border-color: #E5A93B !important; background-color: rgba(229, 169, 59, 0.05) !important; }
.method-box { transition: all 0.2s ease-in-out; }
.method-box:hover { background-color: rgba(0,0,0,0.02) !important; }
.border-dashed { border-style: dashed !important; }
</style>

<?php include_once 'includes/footer.php'; ?>