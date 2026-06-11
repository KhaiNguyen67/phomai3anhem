// ============================================================
// File: assets/js/main.js
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ===== Khởi tạo AOS (Animate On Scroll) =====
    // Đảm bảo AOS đã được load từ header/footer trước
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 700,       // ms mỗi animation
            easing: 'ease-out-quad',
            once: true,          // chỉ chạy 1 lần khi scroll vào
            offset: 60,          // khoảng cách trigger (px từ bottom viewport)
        });
    }

    // ===== Smooth scroll cho anchor link #about =====
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const target   = document.querySelector(targetId);
            if (!target) return;

            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // ===== Parallax nhẹ cho Hero Banner =====
    const heroBanner = document.querySelector('.hero-banner');
    if (heroBanner) {
        window.addEventListener('scroll', function () {
            const scrollY  = window.scrollY;
            const maxShift = 80; // px tối đa dịch chuyển
            const shift    = Math.min(scrollY * 0.25, maxShift);
            heroBanner.style.backgroundPositionY = `calc(center + ${shift}px)`;
        }, { passive: true });
    }

    // ===== Hover ripple cho các Commitment Card =====
    document.querySelectorAll('.commitment-card').forEach(function (card) {
        card.addEventListener('mouseenter', function () {
            this.style.transition = 'transform 0.25s ease, box-shadow 0.25s ease';
        });
    });

});

//index.js
document.querySelectorAll('.ajax-home-cart').forEach(form => {
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
                alert('Đã thêm sản phẩm vào giỏ hàng thành công!');
            }
        });
    });
});

//product.js
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
                alert('Đã thêm phô mai vào giỏ hàng!');
            }
        });
    });
});

//chitietsanpham.js
function changeQty(amount) {
    let input = document.getElementById('quantity_input');
    let current = parseInt(input.value) || 1;
    let nextValue = current + amount;
    let maxStock = parseInt(input.getAttribute('max')) || 1;
    if (nextValue >= 1 && nextValue <= maxStock) { input.value = nextValue; }
}

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
        }
    });
});

//checkout.js
// ============================================================
// File: assets/js/checkout.js
// Chức năng: Tương tác trang Thanh Toán (AJAX cập nhật giỏ hàng)
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ===== Helpers =====

    /** Format số tiền kiểu Việt Nam: 685000 → "685.000 đ" */
    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }

    /** Lấy row cha .product-row từ một element con bất kỳ */
    function getRow(el) {
        return el.closest('.product-row');
    }

    // ===== AJAX cập nhật số lượng lên server =====

    /**
     * Gửi số lượng mới lên update_cart_ajax.php, sau đó cập nhật
     * thành tiền từng dòng và tổng tiền toàn đơn.
     *
     * @param {string} productId
     * @param {number} qty
     * @param {HTMLElement} inputEl   - ô nhập số lượng
     * @param {HTMLElement} subtotalEl - span hiển thị thành tiền
     */
    function updateCartAjax(productId, qty, inputEl, subtotalEl) {
        if (qty < 1) return;

        // Disable các nút trong row để tránh click liên tục
        const row   = inputEl.closest('.product-row');
        const btns  = row.querySelectorAll('.btn-qty-minus, .btn-qty-plus');
        btns.forEach(b => (b.disabled = true));

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity',   qty);

        fetch('update_cart_ajax.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Cập nhật ô số lượng
                    inputEl.value = qty;

                    // Cập nhật thành tiền dòng
                    if (subtotalEl) {
                        subtotalEl.textContent = formatVND(data.item_subtotal);
                    }

                    // Cập nhật tạm tính + tổng cộng
                    const elSubtotal   = document.getElementById('txt-subtotal');
                    const elGrandTotal = document.getElementById('txt-grand-total');
                    if (elSubtotal)   elSubtotal.textContent   = formatVND(data.grand_total);
                    if (elGrandTotal) elGrandTotal.textContent = formatVND(data.grand_total);

                    // Cập nhật badge giỏ hàng trên navbar
                    const badge = document.querySelector('.navbar .badge');
                    if (badge && data.cart_count !== undefined) {
                        badge.textContent = data.cart_count;
                    }
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi cập nhật giỏ hàng.');
                    // Rollback số lượng hiển thị về giá trị cũ
                    inputEl.value = inputEl.dataset.prev || inputEl.value;
                }
            })
            .catch(err => {
                console.error('Lỗi AJAX checkout:', err);
            })
            .finally(() => {
                // Re-enable nút sau khi xử lý xong
                btns.forEach(b => (b.disabled = false));
            });
    }

    // ===== Nút Giảm (-) =====

    document.querySelectorAll('.btn-qty-minus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const row      = getRow(this);
            const pId      = row.dataset.id;
            const inputEl  = row.querySelector('.input-qty');
            const subtotal = row.querySelector('.item-subtotal');
            let   qty      = parseInt(inputEl.value, 10);

            if (qty > 1) {
                inputEl.dataset.prev = qty; // lưu để rollback nếu lỗi
                updateCartAjax(pId, qty - 1, inputEl, subtotal);
            } else {
                if (confirm('Bạn có muốn xóa sản phẩm phô mai này khỏi đơn hàng?')) {
                    window.location.href = 'cart-action.php?action=remove&id=' + pId;
                }
            }
        });
    });

    // ===== Nút Tăng (+) =====

    document.querySelectorAll('.btn-qty-plus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const row      = getRow(this);
            const pId      = row.dataset.id;
            const inputEl  = row.querySelector('.input-qty');
            const subtotal = row.querySelector('.item-subtotal');
            const qty      = parseInt(inputEl.value, 10);

            inputEl.dataset.prev = qty;
            updateCartAjax(pId, qty + 1, inputEl, subtotal);
        });
    });

    // ===== Xác nhận trước khi submit đặt hàng =====

    const form = document.getElementById('checkout-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const btnSubmit = document.getElementById('btn-submit-order');
            if (btnSubmit) {
                btnSubmit.disabled    = true;
                btnSubmit.innerHTML   = '<span class="spinner-border spinner-border-sm me-2"></span> Đang xử lý...';
            }
        });
    }

});
// 1. Hàm đồng bộ thông tin từ Người mua sang Người nhận khi tích chọn nút
function syncBuyerToReceiver() {
    let isChecked = document.getElementById('chkSameAsBuyer').checked;
    
    let buyerName = document.getElementById('buyerName').value;
    let buyerPhone = document.getElementById('buyerPhone').value;

    if (isChecked) {
        document.getElementById('receiverName').value = buyerName;
        document.getElementById('receiverPhone').value = buyerPhone;
    } else {
        document.getElementById('receiverName').value = "";
        document.getElementById('receiverPhone').value = "";
    }
}

// Lắng nghe sự kiện gõ trực tiếp ở ô mua, nếu đang tích checkbox thì cập nhật song song theo thời gian thực
document.getElementById('buyerName').addEventListener('input', function() {
    if (document.getElementById('chkSameAsBuyer').checked) {
        document.getElementById('receiverName').value = this.value;
    }
});
document.getElementById('buyerPhone').addEventListener('input', function() {
    if (document.getElementById('chkSameAsBuyer').checked) {
        document.getElementById('receiverPhone').value = this.value;
    }
});

// 2. Kích hoạt tính năng kiểm tra thuộc tính required của Form hợp lệ trước khi submit
function triggerSubmitForm() {
    let form = document.getElementById('formCheckout');
    if (form.checkValidity()) {
        executeOrder();
    } else {
        form.reportValidity(); // Tự động báo đỏ/hiện thông báo thiếu trường dữ liệu bắt buộc (*)
    }
}

// 3. GỬI DỮ LIỆU LƯU CSDL QUA AJAX & HIỂN THỊ MÃ QR CODE REALTIME
function executeOrder() {
    // Gom dữ liệu từ các ô Input để chuẩn bị gửi lên Server
    let orderData = {
        receiver_name: document.getElementById('receiverName').value,
        receiver_phone: document.getElementById('receiverPhone').value,
        receiver_address: document.getElementById('receiverAddress').value,
        order_notes: document.getElementById('orderNotes').value
    };

    // Hiển thị trạng thái đang xử lý trên nút bấm để tránh khách click liên tục
    let btnSubmit = document.querySelector('button[onclick="triggerSubmitForm()"]');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang khởi tạo đơn hàng...';

    // Tiến hành gửi ngầm dữ liệu sang file save-order.php bằng Fetch API
    fetch('save-order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Đổi mã đơn hàng hiển thị thành ID thực tế trong CSDL
            document.getElementById('txtOrderCode').innerText = '#' + data.order_id;
            
            // Ẩn vùng điền thông tin, hiển thị vùng quét mã QR mượt mà
            document.getElementById('checkoutFormSection').classList.add('d-none');
            document.getElementById('qrPaymentSection').classList.remove('d-none');
            
            // Cuộn màn hình lên đầu để khách dễ nhìn mã QR ngân hàng
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-wallet2 me-2"></i> Thanh Tán Ngay';
        }
    })
    .catch(error => {
        console.error('Lỗi kết nối kết xuất dữ liệu:', error);
        alert('Không thể kết nối đến máy chủ. Vui lòng thử lại!');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-wallet2 me-2"></i> Thanh Toán Ngay';
    });
}

//cart.js
// ============================================================
// File: assets/js/cart.js
// Chức năng: Tương tác trang Giỏ Hàng
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ===== Helpers =====

    /**
     * Format số tiền kiểu Việt Nam: 685000 → "685.000 đ"
     */
    function formatVND(amount) {
        return amount.toLocaleString('vi-VN') + ' đ';
    }

    /**
     * Tính lại tổng tiền từ tất cả các dòng và cập nhật phần tóm tắt
     */
    function recalcTotal() {
        let grandTotal = 0;

        document.querySelectorAll('#cart-table tbody tr').forEach(function (row) {
            const price    = parseInt(row.dataset.price, 10);
            const qtyInput = row.querySelector('.qty-input');
            const qty      = parseInt(qtyInput.value, 10) || 1;
            const subtotal = price * qty;

            row.querySelector('.item-subtotal').textContent = formatVND(subtotal);
            grandTotal += subtotal;
        });

        document.getElementById('summary-subtotal').textContent = formatVND(grandTotal);
        document.getElementById('summary-total').textContent    = formatVND(grandTotal);
    }

    /**
     * Gửi request cập nhật số lượng lên server (cart-action.php)
     */
    function syncQtyToServer(productId, qty) {
        fetch(`cart-action.php?action=update&id=${productId}&qty=${qty}`, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(function (err) {
            console.warn('Không thể đồng bộ giỏ hàng:', err);
        });
    }

    // ===== Nút tăng / giảm số lượng =====

    document.querySelectorAll('.btn-plus, .btn-minus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id       = this.dataset.id;
            const row      = document.querySelector(`tr[data-id="${id}"]`);
            const input    = row.querySelector('.qty-input');
            let qty        = parseInt(input.value, 10) || 1;

            if (this.classList.contains('btn-plus')) {
                qty = Math.min(qty + 1, 99);
            } else {
                qty = Math.max(qty - 1, 1);
            }

            input.value = qty;
            recalcTotal();
            syncQtyToServer(id, qty);
        });
    });

    // ===== Nhập thẳng số lượng =====

    document.querySelectorAll('.qty-input').forEach(function (input) {
        // Chặn giá trị không hợp lệ khi blur
        input.addEventListener('blur', function () {
            let qty = parseInt(this.value, 10);
            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > 99) qty = 99;
            this.value = qty;
            recalcTotal();
            syncQtyToServer(this.dataset.id, qty);
        });

        // Cập nhật realtime khi gõ
        input.addEventListener('input', function () {
            const qty = parseInt(this.value, 10);
            if (!isNaN(qty) && qty >= 1) {
                recalcTotal();
            }
        });
    });

    // ===== Xác nhận xóa từng sản phẩm =====

    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const name = this.dataset.name || 'sản phẩm này';
            const href = this.getAttribute('href');

            if (confirm(`Bạn có chắc muốn xóa "${name}" khỏi giỏ hàng?`)) {
                window.location.href = href;
            }
        });
    });

    // ===== Xác nhận xóa toàn bộ giỏ hàng =====

    const btnClear = document.querySelector('.btn-clear-cart');
    if (btnClear) {
        btnClear.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
                window.location.href = this.getAttribute('href');
            }
        });
    }

});

//admin-product.js
let modalInstance;

document.addEventListener("DOMContentLoaded", function() {
    modalInstance = new bootstrap.Modal(document.getElementById('productFormModal'));
});

function openAddModal() {
    document.getElementById('modalComponentTitle').innerText = "Thêm Sản Phẩm Khối Mới";
    document.getElementById('formAction').value = "add";
    document.getElementById('productId').value = "";
    document.getElementById('productOldImage').value = "";
    document.getElementById('productName').value = "";
    document.getElementById('productPrice').value = "";
    document.getElementById('productDesc').value = "";
    document.getElementById('productFeatured').checked = false;
    document.getElementById('imageHelpBlock').style.display = "none";
    modalInstance.show();
}

function openEditModal(prod) {
    document.getElementById('modalComponentTitle').innerText = "Cập Nhật Thông Tin Sản Phẩm";
    document.getElementById('formAction').value = "edit";
    document.getElementById('productId').value = prod.id;
    document.getElementById('productOldImage').value = prod.image ? prod.image : "";
    document.getElementById('productName').value = prod.name;
    document.getElementById('productCategoryId').value = prod.category_id;
    document.getElementById('productPrice').value = prod.price;
    document.getElementById('productDesc').value = prod.short_desc ? prod.short_desc : "";
    
    document.getElementById('productFeatured').checked = (prod.is_featured == 1);
    document.getElementById('imageHelpBlock').style.display = "block";
    modalInstance.show();
}

//admin-post.js
function generateSlug(val) {
    let slug = val.toLowerCase();
    slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
    slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
    slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
    slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
    slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
    slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
    slug = slug.replace(/đ/gi, 'd');
    slug = slug.replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    document.getElementById('postSlug').value = slug;
}

// Hàm xử lý bật Modal khi thêm mới bài viết (Khởi tạo trực tiếp khi click để chống lỗi nạp thiếu thư viện)
function openAddPostModal() {
    document.getElementById('modalPostTitle').innerText = "Viết Bài Thảo Luận Mới";
    document.getElementById('formPostAction').value = "add";
    document.getElementById('postUserId').value = "";
    document.getElementById('postOldImage').value = "";
    document.getElementById('postTitle').value = "";
    document.getElementById('postSlug').value = "";
    document.getElementById('postSummary').value = "";
    document.getElementById('postContent').value = "";
    
    // Kiểm tra tính sẵn sàng của Bootstrap JS trước khi kích hoạt hiển thị
    if (typeof bootstrap !== 'undefined') {
        let postFormModalEl = document.getElementById('postFormModal');
        let myModal = bootstrap.Modal.getInstance(postFormModalEl) || new bootstrap.Modal(postFormModalEl);
        myModal.show();
    } else {
        alert("Lỗi hệ thống: Chưa tìm thấy thư viện Bootstrap JavaScript! Vui lòng kiểm tra lại xem file header.php hoặc footer.php đã nhúng Bootstrap đúng cách chưa.");
    }
}

// Hàm xử lý đổ dữ liệu và bật Modal khi sửa bài viết
function openEditPostModal(post) {
    document.getElementById('modalPostTitle').innerText = "Chỉnh Sửa Nội Dung Bài Viết";
    document.getElementById('formPostAction').value = "edit";
    document.getElementById('postUserId').value = post.id;
    document.getElementById('postOldImage').value = post.image ? post.image : "";
    document.getElementById('postTitle').value = post.title;
    document.getElementById('postSlug').value = post.slug;
    document.getElementById('postSummary').value = post.summary ? post.summary : "";
    document.getElementById('postContent').value = post.content;
    
    // Kiểm tra tính sẵn sàng của Bootstrap JS trước khi kích hoạt hiển thị
    if (typeof bootstrap !== 'undefined') {
        let postFormModalEl = document.getElementById('postFormModal');
        let myModal = bootstrap.Modal.getInstance(postFormModalEl) || new bootstrap.Modal(postFormModalEl);
        myModal.show();
    } else {
        alert("Lỗi hệ thống: Chưa tìm thấy thư viện Bootstrap JavaScript! Vui lòng kiểm tra lại xem file header.php hoặc footer.php đã nhúng Bootstrap đúng cách chưa.");
    }
}

//admin-order.js
// Xử lý gửi ngầm lệnh cập nhật trạng thái đơn hàng mượt mà bằng Fetch API
document.querySelectorAll('.change-order-status').forEach(select => {
    select.addEventListener('change', function() {
        let orderId = this.getAttribute('data-id');
        let statusVal = this.value;

        let formData = new FormData();
        formData.append('update_status', true);
        formData.append('order_id', orderId);
        formData.append('status', statusVal);

        fetch('orders.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => { 
            if(data.status === 'success') {
                alert('Cập nhật trạng thái đơn hàng #' + orderId + ' thành công!'); 
            } 
        })
        .catch(err => console.error("Lỗi:", err));
    });
});
