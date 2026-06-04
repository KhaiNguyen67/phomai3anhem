<?php
// ============================================================
// File: product.php
// Chức năng: Giao diện danh sách sản phẩm phía khách hàng (PDO)
//            Tích hợp hiển thị nhãn Khuyến Mãi và giá ưu đãi trực quan
// ============================================================
include_once 'config/db.php';
include_once 'includes/header.php';

// Lấy danh sách danh mục để đổ vào bộ lọc
$stmt_cate = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt_cate->fetchAll();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$cate_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Câu lệnh truy vấn chính (lấy thêm các trường sale_price, is_on_sale, is_featured)
$sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1";
$params = [];
if (!empty($search)) { $sql .= " AND p.name LIKE ?"; $params[] = "%$search%"; }
if ($cate_id > 0) { $sql .= " AND p.category_id = ?"; $params[] = $cate_id; }
$sql .= " ORDER BY p.id DESC";

$stmt_prod = $pdo->prepare($sql);
$stmt_prod->execute($params);
$products = $stmt_prod->fetchAll();
?>

<div class="container my-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h1 class="fw-bold">Danh Mục Phô Mai Cao Cấp</h1>
        <p class="text-muted">Khám phá thế giới hương vị phô mai phong cách châu Âu được tuyển chọn kỹ lưỡng</p>
    </div>

    <div class="glass-card p-4 mb-5" data-aos="fade-up">
        <form method="GET" action="product.php" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Tìm tên phô mai..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="0">--- Tất cả phân loại ---</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($cate_id == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?> 
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-gold"><i class="bi bi-filter me-2"></i>Áp Dụng Lọc</button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        <?php if (count($products) > 0): ?>
            <?php foreach($products as $prod): ?>
                <div class="col-sm-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white custom-product-card position-relative">
                        
                        <?php if (isset($prod['is_on_sale']) && $prod['is_on_sale'] == 1): ?>
                            <span class="position-absolute top-0 start-0 m-3 badge bg-danger px-2.5 py-1.5 small rounded-pill shadow-sm animate-pulse" style="z-index: 2;">
                                <i class="bi bi-tag-fill me-1"></i>KHUYẾN MÃI
                            </span>
                        <?php elseif ($prod['is_featured'] == 1): ?>
                            <span class="position-absolute top-0 start-0 m-3 badge bg-warning text-dark px-2.5 py-1.5 small rounded-pill shadow-sm" style="z-index: 2;">
                                Bán chạy
                            </span>
                        <?php endif; ?>

                        <div class="bg-light text-center p-3 d-flex align-items-center justify-content-center" style="height: 200px;">
                            <a href="chitietsanpham.php?id=<?php echo $prod['id']; ?>" class="w-100 h-100 d-flex align-items-center justify-content-center img-container">
                                <?php $img = (!empty($prod['image']) && file_exists('assets/img/'.$prod['image'])) ? 'assets/img/'.$prod['image'] : 'https://images.unsplash.com/photo-1528750994863-30f4a7c05267?q=80&w=400'; ?>
                                <img src="<?php echo $img; ?>" class="img-fluid rounded-3" style="max-height: 160px; object-fit: contain;" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                            </a>
                        </div>
                        
                        <div class="card-body d-flex flex-column p-4">
                            <span class="text-uppercase text-warning fw-bold mb-1" style="font-size: 0.75rem;">
                                <?php echo htmlspecialchars($prod['category_name']); ?>
                            </span>
                            
                            <h6 class="fw-bold my-1 product-title">
                                <a href="chitietsanpham.php?id=<?php echo $prod['id']; ?>" class="text-decoration-none transition-color">
                                    <?php echo htmlspecialchars($prod['name']); ?>
                                </a>
                            </h6>
                            
                            <p class="text-muted small mb-3 flex-grow-1 card-desc">
                                <?php echo htmlspecialchars($prod['short_desc']); ?>
                            </p>
                            
                            <div class="mb-3">
                                <span class="badge bg-light text-dark border">Xuất xứ: <?php echo htmlspecialchars($prod['origin'] ?? 'Đang cập nhật'); ?></span>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <div>
                                    <?php if (isset($prod['is_on_sale']) && $prod['is_on_sale'] == 1 && !empty($prod['sale_price'])): ?>
                                        <span class="fw-bold text-danger d-block fs-5"><?php echo number_format($prod['sale_price'], 0, ',', '.'); ?> đ</span>
                                        <small class="text-muted text-decoration-line-through d-block" style="font-size: 0.8rem;"><?php echo number_format($prod['price'], 0, ',', '.'); ?> đ</small>
                                    <?php else: ?>
                                        <span class="fw-bold text-danger d-block fs-5"><?php echo number_format($prod['price'], 0, ',', '.'); ?> đ</span>
                                    <?php endif; ?>
                                </div>
                                
                                <form class="ajax-cart-form">
                                    <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-gold p-2 lh-1 rounded-circle" title="Thêm vào giỏ hàng">
                                        <i class="bi bi-cart-plus fs-5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center my-5">
                <i class="bi bi-emoji-frown display-4 text-muted"></i>
                <p class="mt-3 text-muted">Không tìm thấy sản phẩm phô mai nào phù hợp với bộ lọc.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Thiết lập hiệu ứng mượt mà 3D chuyển động cho Card */
.custom-product-card {
    transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
}

/* Hiệu ứng nâng nhẹ và đổ bóng chuẩn UI/UX khi di chuột */
.custom-product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.12) !important;
}

/* Màu mặc định của liên kết tên sản phẩm */
.product-title a {
    color: #212529;
    transition: color 0.3s ease;
}

/* Tự động chuyển màu chữ tên sản phẩm sang tông Vàng Gold thương hiệu khi Hover Card */
.custom-product-card:hover .product-title a {
    color: #E5A93B; 
}

/* Hiệu ứng Scale phóng to ảnh nhẹ nhàng */
.img-container img {
    transition: transform 0.4s ease;
}
.custom-product-card:hover .img-container img {
    transform: scale(1.06);
}

.card-desc {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Hiệu ứng nhấp nháy tinh tế thu hút người mua chú ý vào nhãn Khuyến Mãi */
@keyframes pulse {
    0% { opacity: 0.9; }
    50% { opacity: 1; transform: scale(1.03); }
    100% { opacity: 0.9; }
}
.animate-pulse {
    animation: pulse 2s infinite ease-in-out;
}
</style>

<script>
// Logic xử lý giỏ hàng bất đồng bộ bằng Fetch API
document.querySelectorAll('.ajax-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
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
                alert('Đã thêm phô mai vào giỏ hàng thành công!');
            }
        })
        .catch(error => console.error('Lỗi hệ thống giỏ hàng:', error));
    });
});
</script>

<?php include_once 'includes/footer.php'; ?>