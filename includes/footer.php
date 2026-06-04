</main>

<style>
    .footer-link {
        transition: all 0.3s ease;
    }
    .footer-link:hover {
        color: var(--primary-gold) !important;
        padding-left: 6px;
    }
    .social-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.6);
        transition: all 0.3s ease;
    }
    .social-icon:hover {
        background: var(--primary-gold);
        color: #000 !important;
        transform: translateY(-3px);
    }
    .payment-badge {
        filter: grayscale(1) opacity(0.6);
        transition: all 0.3s ease;
        height: 22px;
    }
    .payment-badge:hover {
        filter: grayscale(0) opacity(1);
    }
</style>

<footer class="bg-dark text-white pt-5 pb-4 mt-5 border-top border-secondary border-opacity-25">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="d-flex align-items-center mb-3">
                    <img src="assets/img/background3.jpg" alt="Logo Phô Mai 3 Anh Em" class="rounded-circle me-2" style="width: 120px; height: 120px; object-fit: cover; border: 2px solid var(--primary-gold);">
                    <h5 class="fw-bold mb-0 fs-2" style="color: var(--primary-gold); letter-spacing: 0.5px;">
                        Phô Mai 3 Anh Em
                    </h5>
                </div>
                <p class="text-white-50 small lh-lg mb-4">
                    Mang tinh hoa ẩm thực Châu Âu đến bàn ăn gia đình Việt. Chúng tôi chuyên cung cấp các loại phô mai nhập khẩu chính ngạch với chất lượng hoàn hảo và quy trình bảo quản nghiêm ngặt.
                </p>
                <div class="d-flex gap-2">
                    <a href="https://www.facebook.com/groups/968864402795937" class="social-icon text-decoration-none"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/p/DZBtjFAE02C/" class="social-icon text-decoration-none"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon text-decoration-none"><i class="bi bi-tiktok"></i></a>
                    <a href="#" class="social-icon text-decoration-none"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 px-md-4" data-aos="fade-up" data-aos-delay="100">
                <h5 class="fw-bold mb-3 position-relative pb-2" style="letter-spacing: 0.5px;">
                    Khám Phá
                    <span class="position-absolute bottom-0 start-0" style="width: 30px; height: 2px; background-color: var(--primary-gold) !important;"></span>
                </h5>
                <ul class="list-unstyled small pt-2">
                    <li class="mb-2">
                        <a href="index.php" class="text-white-50 text-decoration-none footer-link d-inline-block">
                            <i class="bi bi-chevron-right small me-1"></i> Trang chủ
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="product.php" class="text-white-50 text-decoration-none footer-link d-inline-block">
                            <i class="bi bi-chevron-right small me-1"></i> Danh sách sản phẩm
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="Blog.php" class="text-white-50 text-decoration-none footer-link d-inline-block">
                            <i class="bi bi-chevron-right small me-1"></i> Tin tức ẩm thực
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="contact.php" class="text-white-50 text-decoration-none footer-link d-inline-block">
                            <i class="bi bi-chevron-right small me-1"></i> Liên hệ hệ thống
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-5 col-md-12" data-aos="fade-up" data-aos-delay="200">
                <h5 class="fw-bold mb-3 position-relative pb-2" style="letter-spacing: 0.5px;">
                    Thông Tin Liên Hệ
                    <span class="position-absolute bottom-0 start-0" style="width: 30px; height: 2px; background-color: var(--primary-gold) !important;"></span>
                </h5>
                <div class="pt-2">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1" style="color: var(--primary-gold) !important;">
                            <i class="bi bi-geo-alt-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-white fw-semibold small">Địa chỉ trụ sở:</h6>
                            <p class="text-white-50 small mb-0">Quận 12, Thành phố Hồ Chí Minh, Việt Nam</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1" style="color: var(--primary-gold) !important;">
                            <i class="bi bi-envelope-open-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-white fw-semibold small">Email hỗ trợ:</h6>
                            <p class="text-white-50 small mb-0">contact@phomai3anhem.vn</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <div class="me-3 mt-1" style="color: var(--primary-gold) !important;">
                            <i class="bi bi-telephone-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-white fw-semibold small">Hotline tư vấn:</h6>
                            <p class="text-white-50 small mb-0 fw-bold" style="color: var(--primary-gold) !important;">1900 3667 (08:00 - 21:00)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4 opacity-25">

        <div class="row align-items-center g-3 text-center text-md-start">
            <div class="col-md-6 text-white-50 small">
                <p class="mb-0">&copy; 2026 <span style="color: var(--primary-gold);">Phô Mai 3 Anh Em</span>. Mọi quyền được bảo lưu.</p>
            </div>
            <div class="col-md-6 text-md-end text-center d-flex justify-content-center justify-content-md-end gap-3 align-items-center">
                <span class="text-white-50 small d-none d-sm-inline">Phương thức thanh toán:</span>
                <i class="bi bi-credit-card-2-front fs-4 text-white-50 payment-badge" title="Thẻ ATM/Visa"></i>
                <i class="bi bi-wallet2 fs-4 text-white-50 payment-badge" title="Ví điện tử"></i>
                <i class="bi bi-truck fs-4 text-white-50 payment-badge" title="Thanh toán khi nhận hàng (COD)"></i>
            </div>
        </div>
    </div>
</footer>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 80
    });
</script>
</body>
</html>