<?php
include_once 'config/db.php';
include_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p 
                        JOIN categories c ON p.category_id = c.id 
                        WHERE p.id = ? AND p.is_active = 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container my-5 text-center py-5'>
            <div class='alert alert-warning d-inline-block px-5'>Sản phẩm phô mai này không tồn tại hoặc đã ngừng kinh doanh.</div>
            <br><a href='product.php' class='btn btn-gold mt-3'>Quay lại cửa hàng</a>
          </div>";
    include_once 'includes/footer.php';
    exit();
}
?>

<div class="container my-5">
    <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-up">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="product.php" class="text-muted text-decoration-none">Sản phẩm</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        
        <div class="col-md-6" data-aos="fade-right">
            <div class="bg-white p-4 rounded-4 shadow-sm border h-100">
                <span class="badge bg-warning text-dark text-uppercase mb-2 px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </span>
                
                <h1 class="fw-bold mb-3 text-dark h3"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="text-center bg-light p-3 rounded-4 mb-4 d-flex align-items-center justify-content-center" style="min-height: 300px;">
                    <?php 
                    $local_image_path = 'assets/img/' . $product['image']; 
                    $image_src = (!empty($product['image']) && file_exists($local_image_path)) ? $local_image_path : 'https://images.unsplash.com/photo-1528750994863-30f4a7c05267?q=80&w=600';
                    ?>
                    <img src="<?php echo $image_src; ?>" class="img-fluid rounded-3 <?php echo ($product['stock'] <= 0) ? 'opacity-50' : ''; ?>" style="max-height: 260px; object-fit: contain;" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>

                <div class="mb-4 py-3 px-4 bg-light rounded-4 text-center border-start border-danger border-3">
                    <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">Giá bán ưu đãi</small>
                    <span class="fs-2 fw-bold text-danger"><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</span>
                </div>

                <form id="detail-cart-form" class="mt-2">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="mb-4 d-flex align-items-center justify-content-between bg-light p-3 rounded-3">
                        <span class="small fw-bold text-secondary">Số lượng mua:</span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group" style="max-width: 130px;">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="changeQty(-1)" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>-</button>
                                <input type="number" id="quantity_input" name="quantity" class="form-control form-control-sm text-center fw-bold" value="<?php echo $product['stock'] > 0 ? '1' : '0'; ?>" min="<?php echo $product['stock'] > 0 ? '1' : '0'; ?>" max="<?php echo $product['stock']; ?>" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="changeQty(1)" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>+</button>
                            </div>
                            <small class="text-muted font-monospace">(Kho: <?php echo $product['stock']; ?>)</small>
                        </div>
                    </div>

                    <?php if ($product['stock'] > 0): ?>
                        <button type="submit" class="btn btn-gold btn-lg w-100 py-3 text-uppercase fs-6 fw-bold shadow-sm rounded-3">
                            <i class="bi bi-cart-plus me-2 fs-5"></i>Thêm Vào Giỏ Hàng
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary btn-lg w-100 py-3 text-uppercase fs-6 fw-bold shadow-sm rounded-3" disabled>
                            <i class="bi bi-dash-circle me-2 fs-5"></i>Tạm hết hàng
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="col-md-6" data-aos="fade-left">
            <div class="d-flex flex-column gap-4 h-100">
                
                <div class="glass-card p-4 shadow-sm border bg-white rounded-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2"><i class="bi bi-info-circle me-2 text-warning"></i>Thông số sản phẩm</h5>
                    <div class="row g-3 small">
                        <div class="col-6 text-muted">Xuất xứ thương hiệu:</div>
                        <div class="col-6 text-end text-dark fw-semibold"><?php echo htmlspecialchars($product['origin'] ?? 'Đang cập nhật'); ?></div>
                        <hr class="my-1 border-light">
                        <div class="col-6 text-muted">Trọng lượng đóng gói:</div>
                        <div class="col-6 text-end text-dark fw-semibold"><?php echo number_format($product['weight_gram'] ?? 200, 0, ',', '.'); ?> gram</div>
                        <hr class="my-1 border-light">
                        <div class="col-6 text-muted">Tình trạng kho hàng:</div>
                        <div class="col-6 text-end fw-semibold <?php echo $product['stock'] > 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo $product['stock'] > 0 ? 'Còn hàng sẵn tại shop' : 'Hết hàng tạm thời'; ?>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-4 border-start border-warning border-3 shadow-sm bg-white rounded-4 flex-grow-1">
                    <h5 class="fw-bold mb-3 text-dark d-flex align-items-center">
                        <i class="bi bi-journal-text text-warning me-2 fs-4"></i>
                        Đặc Điểm & Hương Vị Chi Tiết
                    </h5>
                    <p class="text-muted small mb-3 border-bottom pb-2 style-italic">
                        <?php echo htmlspecialchars($product['short_desc']); ?>
                    </p>
                    <div class="text-secondary lh-lg" style="white-space: pre-line; text-align: justify; font-size: 0.95rem;">
                        <?php echo htmlspecialchars($product['description'] ?? 'Nội dung chi tiết về đặc tính và cách dùng của loại phô mai thượng hạng này đang được cập nhật.'); ?>
                    </div>
                </div>
                
            </div>
        </div>

    </div>
</div>

<script>
function changeQty(amount) {
    let input = document.getElementById('quantity_input');
    if(!input) return;
    let current = parseInt(input.value) || 1;
    let nextValue = current + amount;
    let maxStock = parseInt(input.getAttribute('max')) || 0;
    let minQty = parseInt(input.getAttribute('min')) || 1;
    
    if (nextValue >= minQty && nextValue <= maxStock) { 
        input.value = nextValue; 
    }
}

// Kiểm tra tính hợp lệ thủ công nếu khách tự gõ số vào ô input
document.getElementById('quantity_input')?.addEventListener('change', function() {
    let maxStock = parseInt(this.getAttribute('max')) || 0;
    let minQty = parseInt(this.getAttribute('min')) || 1;
    let val = parseInt(this.value) || minQty;
    
    if(val > maxStock) {
        alert('Số lượng trong kho chỉ còn ' + maxStock + ' sản phẩm!');
        this.value = maxStock;
    } else if (val < minQty) {
        this.value = minQty;
    }
});

document.getElementById('detail-cart-form').addEventListener('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    fetch('cart.php?action=add', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            let badge = document.querySelector('.cart-badge');
            if(badge) {
                badge.innerText = data.total_items;
                badge.classList.remove('d-none');
            }
            alert('Đã cập nhật sản phẩm vào giỏ hàng thành công!');
        } else {
            alert(data.message || 'Có lỗi xảy ra.');
        }
    });
});
</script>

<?php include_once 'includes/footer.php'; ?>