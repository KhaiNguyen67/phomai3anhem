<?php
// ============================================================
// File: cart.php
// Chức năng: Logic giỏ hàng xử lý đồng thời AJAX & Form truyền thống
//            Tích hợp kiểm tra tồn kho thời gian thực từ CSDL
// ============================================================
include_once 'config/db.php';

if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// 1. NGHIỆP VỤ: THÊM SẢN PHẨM (HỖ TRỢ AJAX VÀ KIỂM KHO TRỰC TIẾP TỪ DB)
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if ($quantity < 1) { $quantity = 1; }

    // Luôn truy vấn DB để lấy số lượng tồn kho mới nhất
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        // Tính tổng số lượng khách muốn mua (đã có trong giỏ + số lượng mới bấm thêm)
        $current_qty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;
        $total_requested = $current_qty + $quantity;

        // Chặn ngay nếu sản phẩm đã hết hàng trong hệ thống
        if ($product['stock'] <= 0) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Rất tiếc, sản phẩm này hiện tại đã hết hàng!']);
                exit();
            }
            header('Location: product.php');
            exit();
        }

        // Chặn nếu tổng số lượng vượt quá kho hàng hiện tại
        if ($total_requested > $product['stock']) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'message' => "Không thể thêm! Kho còn {$product['stock']} chiếc, giỏ hàng của bạn đã có {$current_qty} chiếc."
                ]);
                exit();
            }
            $_SESSION['cart'][$product_id]['quantity'] = $product['stock'];
        } else {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] = $total_requested;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity,
                    'stock' => $product['stock']
                ];
            }
        }

        // Cập nhật lại giá trị tồn kho mới nhất vào Session giỏ hàng
        $_SESSION['cart'][$product_id]['stock'] = $product['stock'];

        // Tính tổng số lượng mặt hàng hiện có trong giỏ để làm Badge hiển thị
        $total_items = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_items += $item['quantity'];
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'total_items' => $total_items]);
            exit();
        }

        header('Location: cart.php');
        exit();
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã bị ẩn.']);
            exit();
        }
    }
}

// 2. NGHIỆP VỤ: XÓA SẢN PHẨM KHỎI GIỎ HÀNG
if ($action === 'delete') {
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header('Location: cart.php');
    exit();
}

// 3. NGHIỆP VỤ: CẬP NHẬT SỐ LƯỢNG (Kiểm kho trực tiếp từ Database khi sửa ô Input)
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_qty']) && is_array($_POST['update_qty'])) {
        foreach ($_POST['update_qty'] as $id => $qty) {
            $id = (int)$id;
            $qty = (int)$qty;
            if (isset($_SESSION['cart'][$id])) {
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$id]);
                } else {
                    // Truy vấn kiểm tra kho thực tế trong DB tránh việc khách cố tình đổi trị số HTML
                    $stmtCheck = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
                    $stmtCheck->execute([$id]);
                    $realStock = $stmtCheck->fetchColumn();

                    if ($realStock === false) {
                        unset($_SESSION['cart'][$id]);
                        continue;
                    }

                    if ($qty > $realStock) {
                        $_SESSION['cart'][$id]['quantity'] = $realStock;
                        $_SESSION['cart'][$id]['stock'] = $realStock;
                    } else {
                        $_SESSION['cart'][$id]['quantity'] = $qty;
                        $_SESSION['cart'][$id]['stock'] = $realStock;
                    }
                }
            }
        }
    }
    header('Location: cart.php');
    exit();
}

// ============================================================
// PHẦN GIAO DIỆN: HIỂN THỊ GIỎ HÀNG
// ============================================================
include_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="mb-4" data-aos="fade-up">
        <h2 class="fw-bold"><i class="bi bi-bag-check me-2 text-warning"></i>Giỏ Hàng Của Bạn</h2>
        <p class="text-muted small">Kiểm tra lại danh sách phô mai trước khi tiến hành thanh toán</p>
        <a href="order-history.php" class="btn btn-warning btn-sm fw-bold shadow-sm px-3 text-dark">
            <i class="bi bi-clock-history me-1"></i>Lịch sử đơn hàng
        </a>
    </div>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div class="row g-4">
            <div class="col-lg-8" data-aos="fade-right">
                <form method="POST" action="cart.php?action=update" class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="table-responsive">
                        <table class="table align-middle m-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Sản phẩm</th>
                                    <th>Giá tiền</th>
                                    <th style="width: 140px;">Số lượng</th>
                                    <th>Thành tiền</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grand_total = 0;
                                foreach ($_SESSION['cart'] as $id => $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $grand_total += $subtotal;
                                    $img_src = (!empty($item['image']) && file_exists('assets/img/' . $item['image'])) ? 'assets/img/' . $item['image'] : 'https://images.unsplash.com/photo-1528750994863-30f4a7c05267?q=80&w=100';
                                    
                                    // Đảm bảo đồng bộ thông tin kho hiển thị
                                    $item_stock = isset($item['stock']) ? $item['stock'] : 99;
                                ?>
                                <tr>
                                    <td class="text-start d-flex align-items-center gap-3">
                                        <img src="<?php echo $img_src; ?>" class="rounded-3 border bg-light" style="width: 55px; height: 55px; object-fit: contain;">
                                        <div>
                                            <h6 class="fw-bold m-0 text-dark"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <small class="text-muted">Kho: <span class="badge bg-light text-secondary border"><?php echo $item_stock; ?> chiếc</span></small>
                                        </div>
                                    </td>
                                    <td class="fw-semibold"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <input type="number" name="update_qty[<?php echo $id; ?>]" class="form-control form-control-sm text-center fw-bold rounded-3" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item_stock; ?>" onchange="this.form.submit()">
                                    </td>
                                    <td class="text-danger fw-bold"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <a href="cart.php?action=delete&id=<?php echo $id; ?>" class="text-secondary hover-danger fs-5" onclick="return confirm('Xóa sản phẩm này?')">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <a href="product.php" class="btn btn-sm btn-outline-secondary rounded-3"><i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm</a>
                        <button type="submit" class="btn btn-sm btn-dark rounded-3"><i class="bi bi-arrow-clockwise me-1"></i> Cập nhật giỏ hàng</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4" data-aos="fade-left">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Tạm tính giỏ hàng:</span>
                        <span><?php echo number_format($grand_total, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span>Phí giao hàng:</span>
                        <span class="text-success">Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold">Tổng tiền:</span>
                        <span class="fs-4 fw-bold text-danger"><?php echo number_format($grand_total, 0, ',', '.'); ?> đ</span>
                    </div>
                    <a href="checkout.php" class="btn btn-gold btn-lg w-100 py-3 text-uppercase fs-6 fw-bold shadow-sm rounded-3">
                        Tiến Hành Thanh Toán <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center my-5 py-5 bg-white rounded-4 shadow-sm border" data-aos="zoom-in">
            <i class="bi bi-cart-x text-muted display-1"></i>
            <h5 class="fw-bold mt-4 text-dark">Giỏ hàng trống!</h5>
            <p class="text-muted small">Hãy quay lại chọn những miếng phô mai ngon lành nhé.</p>
            <a href="product.php" class="btn btn-gold px-4 py-2 mt-2 text-white rounded-pill shadow-sm"><i class="bi bi-shop me-1"></i> Đến cửa hàng ngay</a>
        </div>
    <?php endif; ?>
</div>

<style>.hover-danger:hover { color: #dc3545 !important; }</style>
<?php include_once 'includes/footer.php'; ?>