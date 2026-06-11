-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:2333
-- Generation Time: Jun 11, 2026 at 04:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phomai3anhem`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `created_at`) VALUES
(1, 'Phô Mai Cứng', 'pho-mai-cung', 'Phô mai được ủ lâu năm, kết cấu chắc, hương vị đậm đà và phức tạp.', 'bi-gem', '2026-05-25 15:19:14'),
(2, 'Phô Mai Tươi', 'pho-mai-tuoi', 'Phô mai chưa qua ủ, kết cấu mềm mịn, hương vị thanh nhẹ, béo nhẹ.', 'bi-droplet', '2026-05-25 15:19:14'),
(3, 'Phô Mai Xanh', 'pho-mai-xanh', 'Phô mai có vân xanh từ nấm mốc Penicillium, hương vị nồng nàn độc đáo.', 'bi-wind', '2026-05-25 15:19:14'),
(4, 'Phô Mai Nửa Cứng', 'pho-mai-nua-cung', 'Phô mai ủ vừa, kết cấu dẻo, cân bằng giữa mềm và cứng.', 'bi-layers', '2026-05-25 15:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `fullname`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(3, 'duy khai', 'nduykhai@gmail.com', '0901234567', '123456', '123456', 'replied', '2026-06-04 08:09:03'),
(4, 'khải', 'nduykhai805@gmail.com', '0901234567', 'sibidi', '367 tom ngọc', 'replied', '2026-06-08 02:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `receiver_name` varchar(255) DEFAULT NULL,
  `receiver_phone` varchar(20) DEFAULT NULL,
  `receiver_address` text DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `note` text DEFAULT NULL,
  `total_money` int(11) NOT NULL,
  `payment_method` varchar(20) NOT NULL DEFAULT 'qr',
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `payment_proof` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `receiver_name`, `receiver_phone`, `receiver_address`, `full_name`, `email`, `phone`, `address`, `note`, `total_money`, `payment_method`, `status`, `payment_proof`, `created_at`) VALUES
(1, 4, NULL, NULL, NULL, 'Tung', 'Tung@gmail.com', '090123456', '123,a,b,c', '367', 360000, 'qr', 'pending', NULL, '2026-05-28 02:31:03'),
(2, 4, NULL, NULL, NULL, 'Tung', 'Tung@gmail.com', '091023901', '367,a,b,c', '132', 450000, 'qr', 'pending', NULL, '2026-05-28 02:32:15'),
(3, 4, NULL, NULL, NULL, 'Tung', 'Tung@gmail.com', '0912039101', '123123,a,b,c', '', 580000, 'qr', 'pending', NULL, '2026-05-28 02:34:25'),
(4, 4, NULL, NULL, NULL, 'Tung', 'Tung@gmail.com', '132123123123', 'a,b,c', '367', 340000, 'qr', 'pending', NULL, '2026-05-28 02:35:02'),
(5, 4, NULL, NULL, NULL, 'Tung', 'Tung@gmail.com', '91283901890', 'Tung', '', 340000, 'qr', 'pending', NULL, '2026-05-28 02:56:47'),
(6, 4, NULL, NULL, NULL, 'Tung', 'Tung@gmail.com', '1234567890', '123,Tung', '', 340000, 'qr', 'pending', NULL, '2026-05-28 02:57:37'),
(7, 4, NULL, NULL, NULL, 'Tung', 'Tung@gmail.com', '1234567892', '123123123', '', 360000, 'qr', 'pending', NULL, '2026-05-28 02:59:31'),
(8, 4, NULL, NULL, NULL, 'Tung', 'Tung@gmail.com', '1234567890', '123', 'a', 360000, 'qr', 'pending', NULL, '2026-05-28 03:01:14'),
(9, 4, NULL, NULL, NULL, 'Tung', 'Tung@gmail.com', '1923809001', '123', '', 340000, 'qr', 'cancelled', NULL, '2026-05-28 03:03:10'),
(10, NULL, NULL, NULL, NULL, 'SOn', '', '1234567809', '1231231231', '123333', 1210000, 'qr', 'cancelled', NULL, '2026-05-28 06:45:30'),
(31, 4, 'Tung', '123', '123', 'Tung', 'Tung@gmail.com', '123', '123', '123', 360000, 'qr', 'cancelled', 'assets/img/proofs/qr_31_1780472595.jpg', '2026-06-03 07:43:07'),
(32, 4, 'Tung', '123', '123', 'Tung', 'Tung@gmail.com', '123', '123', '123', 360000, 'qr', 'completed', 'assets/img/proofs/qr_32_1780472680.jpg', '2026-06-03 07:44:34'),
(33, 4, 'Tung', '123', '213', 'Tung', 'Tung@gmail.com', '123', '213', '123', 340000, 'qr', 'pending', NULL, '2026-06-03 07:46:19'),
(34, 4, 'Tung', '123', '123', 'Tung', 'Tung@gmail.com', '123', '123', '123', 340000, 'qr', 'pending', NULL, '2026-06-03 07:51:48'),
(35, 4, 'Tung', '123', '123', 'Tung', 'Tung@gmail.com', '123', '123', '123', 450000, 'qr', 'completed', 'assets/img/proofs/qr_35_1780473129.jpg', '2026-06-03 07:52:04'),
(36, 4, 'Tung', '09087654321', '123', 'Tung', 'Tung@gmail.com', '09087654321', '123', '123', 145000, 'qr', 'completed', 'assets/img/proofs/qr_36_1780533255.png', '2026-06-04 00:34:05'),
(37, 4, 'Tung', '123', '213', 'Tung', 'Tung@gmail.com', '123', '213', '123', 360000, 'qr', 'completed', 'assets/img/proofs/qr_37_1780535218.png', '2026-06-04 01:05:32'),
(38, 12, 'khai22', '1234567890', '1', 'khai22', 'duykhain4@gmail.com', '1234567890', '1', '2', 30600000, 'qr', 'shipping', 'assets/img/proofs/qr_38_1780557611.jpg', '2026-06-04 07:20:06'),
(39, 4, 'Tung', '1234567889', '1', 'Tung', 'Tung@gmail.com', '1234567889', '1', '2', 340000, 'qr', 'cancelled', NULL, '2026-06-04 07:38:22'),
(40, 4, 'Tung', '1234567890', '3', 'Tung', 'Tung@gmail.com', '1234567890', '3', '3', 340000, 'qr', 'completed', 'assets/img/proofs/qr_40_1780558991.png', '2026-06-04 07:43:01'),
(41, 4, 'Tung', '1234567890', '12', 'Tung', 'Tung@gmail.com', '1234567890', '12', '34', 505000, 'qr', 'completed', 'assets/img/proofs/qr_41_1780560014.png', '2026-06-04 08:00:01'),
(42, 14, 'khai', '0901234568', 'tung', 'khai', 'nduykhai805@gmail.com', '0901234568', 'tung', 'tung', 720000, 'qr', 'cancelled', 'assets/img/proofs/qr_42_1780898913.jpg', '2026-06-08 06:08:03'),
(43, 4, 'Tung', '0901234567', 'tung', 'Tung', 'Tung@gmail.com', '0901234567', 'tung', 'tung', 360000, 'qr', 'unpaid', NULL, '2026-06-08 06:46:01'),
(45, 4, 'Tung', '1234567890', '123', 'Tung', 'Tung@gmail.com', '1234567890', '123', 'a', 1380000, 'qr', 'pending_proof', 'assets/img/proofs/qr_45_1780903539.png', '2026-06-08 07:25:16'),
(47, 4, 'Tung', '091234567', '123', 'Tung', 'Tung@gmail.com', '091234567', '123', '', 340000, 'vnpay', 'unpaid', NULL, '2026-06-08 07:48:36'),
(48, 4, 'Tung', '1293819283', 'abc', 'Tung', 'Tung@gmail.com', '1293819283', 'abc', 'abc', 340000, 'vnpay', 'unpaid', NULL, '2026-06-08 07:50:46'),
(49, 4, 'Tung', '091234567', '123', 'Tung', 'Tung@gmail.com', '091234567', '123', '', 340000, 'vnpay', 'shipping', NULL, '2026-06-08 07:52:21'),
(50, 4, 'Tung', '091234567', '123', 'Tung', 'Tung@gmail.com', '091234567', '123', '', 340000, 'vnpay', 'shipping', NULL, '2026-06-08 07:54:00'),
(51, 4, 'Tung', '091234567', 'abc', 'Tung', 'Tung@gmail.com', '091234567', 'abc', '123', 340000, 'vnpay', 'shipping', NULL, '2026-06-08 07:58:38'),
(52, 4, 'Tung', '0901234567', '123', 'Tung', 'Tung@gmail.com', '0901234567', '123', '', 340000, 'vnpay', 'shipping', NULL, '2026-06-08 08:08:54'),
(53, 4, 'Tung', '0901234567', '123', 'Tung', 'Tung@gmail.com', '0901234567', '123', '', 340000, 'qr', 'shipping', NULL, '2026-06-08 08:42:27'),
(54, 4, 'Tung', '09120930913', '123', 'Tung', 'Tung@gmail.com', '09120930913', '123', '123', 340000, 'vnpay', 'completed', NULL, '2026-06-08 08:42:49'),
(55, 14, 'khai', '0901234568', '123', 'khai', 'nduykhai805@gmail.com', '0901234568', '123', '123', 340000, 'vnpay', 'shipping', NULL, '2026-06-08 08:52:43'),
(56, 4, 'Tung', '098291239', '73', 'Tung', 'Tung@gmail.com', '098291239', '73', '36', 720000, 'cod', 'cancelled', NULL, '2026-06-10 00:35:14'),
(57, 4, 'Tung', '1234567890', '1', 'Tung', 'Tung@gmail.com', '1234567890', '1', '2', 360000, 'vnpay', 'shipping', NULL, '2026-06-10 00:39:56'),
(58, 4, 'Tung', '098654321', '0', 'Tung', 'Tung@gmail.com', '098654321', '0', '0', 340000, 'vnpay', 'shipping', NULL, '2026-06-10 02:16:20'),
(59, 4, 'Tung', '0987654321', '1', 'Tung', 'Tung@gmail.com', '0987654321', '1', '2', 340000, 'vnpay', 'shipping', NULL, '2026-06-10 02:21:58'),
(60, 14, 'khai', '0901234568', '367', 'khai', 'nduykhai805@gmail.com', '0901234568', '367', '367', 340000, 'vnpay', 'completed', NULL, '2026-06-10 02:23:21'),
(61, 4, 'Tung', '918230918', '1', 'Tung', 'Tung@gmail.com', '918230918', '1', '2', 340000, 'vnpay', 'shipping', NULL, '2026-06-10 03:28:35'),
(62, 4, 'Tung', '918230918', '1', 'Tung', 'Tung@gmail.com', '918230918', '1', '2', 340000, 'vnpay', 'shipping', NULL, '2026-06-10 03:28:53'),
(63, 4, 'Tung', '918230918', '1', 'Tung', 'nduykhai805@gmail.com', '918230918', '1', '2', 340000, 'vnpay', 'shipping', NULL, '2026-06-10 03:29:27'),
(64, 14, 'khai', '0901234568', '3', 'khai', 'nduykhai805@gmail.com', '0901234568', '3', '6', 360000, 'vnpay', 'completed', NULL, '2026-06-10 06:01:45'),
(65, 4, 'Tung', '0982940613', 'd73', 'Tung', 'Tung@gmail.com', '0982940613', 'd73', '3 gio', 360000, 'vnpay', 'unpaid', NULL, '2026-06-10 08:28:39'),
(66, 4, 'Tung', '0982940612', '1', 'Tung', 'Tung@gmail.com', '0982940612', '1', '2', 340000, 'vnpay', 'unpaid', NULL, '2026-06-10 08:42:28'),
(67, 4, 'Tung', '0982940613', '1', 'Tung', 'Tung@gmail.com', '0982940613', '1', '2', 340000, 'vnpay', 'unpaid', NULL, '2026-06-11 00:21:54'),
(68, 4, 'Tung', '0982940613', '1', 'Tung', 'Tung@gmail.com', '0982940613', '1', '2', 340000, 'vnpay', 'unpaid', NULL, '2026-06-11 01:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `price`, `quantity`) VALUES
(1, 1, 12, 360000, 1),
(2, 2, 14, 450000, 1),
(3, 3, 10, 580000, 1),
(4, 4, 13, 340000, 1),
(5, 5, 13, 340000, 1),
(6, 6, 13, 340000, 1),
(7, 7, 12, 360000, 1),
(8, 8, 12, 360000, 1),
(9, 9, 13, 340000, 1),
(10, 10, 13, 340000, 1),
(11, 10, 12, 360000, 1),
(12, 10, 11, 510000, 1),
(35, 31, 12, 360000, 1),
(36, 32, 12, 360000, 1),
(37, 33, 13, 340000, 1),
(38, 34, 13, 340000, 1),
(39, 35, 14, 450000, 1),
(40, 36, 8, 145000, 1),
(41, 37, 12, 360000, 1),
(42, 38, 13, 340000, 90),
(43, 39, 13, 340000, 1),
(44, 40, 13, 340000, 1),
(45, 41, 8, 145000, 1),
(46, 41, 12, 360000, 1),
(47, 42, 12, 360000, 2),
(48, 43, 12, 360000, 1),
(51, 45, 13, 340000, 3),
(52, 45, 12, 360000, 1),
(54, 47, 13, 340000, 1),
(55, 48, 13, 340000, 1),
(56, 49, 13, 340000, 1),
(57, 50, 13, 340000, 1),
(58, 51, 13, 340000, 1),
(59, 52, 13, 340000, 1),
(60, 53, 13, 340000, 1),
(61, 54, 13, 340000, 1),
(62, 55, 13, 340000, 1),
(63, 56, 12, 360000, 2),
(64, 57, 12, 360000, 1),
(65, 58, 13, 340000, 1),
(66, 59, 13, 340000, 1),
(67, 60, 13, 340000, 1),
(68, 61, 13, 340000, 1),
(69, 62, 13, 340000, 1),
(70, 63, 13, 340000, 1),
(71, 64, 12, 360000, 1),
(72, 65, 12, 360000, 1),
(73, 66, 13, 340000, 1),
(74, 67, 13, 340000, 1),
(75, 68, 13, 340000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT 'default-post.jpg',
  `summary` varchar(500) DEFAULT NULL,
  `content` text NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `image`, `summary`, `content`, `is_featured`, `created_at`) VALUES
(3, 'Bí Quyết Làm Món Phô Mai Kéo Sợi Siêu Đỉnh Với Mozzarella \"3 Anh Em\"', 'bi-quyet-lam-mon-pho-mai-keo-soi-sieu-dinh-voi-mozzarella-3-anh-em', 'post_1780274156.jpg', 'Khám phá cách tạo nên lớp phô mai dai ngon, béo ngậy và kéo sợi hoàn hảo cho các món pizza, gà cay hay phô mai que rộp rộp vạn người mê.', 'Nếu bạn là một tín đồ của những món ăn vặt đường phố hay đam mê ẩm thực Ý, chắc chắn không thể bỏ qua sức hấp dẫn của lớp phô mai kéo sợi óng ả. Với dòng Phô mai Mozzarella kéo sợi từ thương hiệu Phô Mai 3 Anh Em, việc mang \"Hương vị chuẩn Âu\" vào căn bếp nhà bạn chưa bao giờ dễ dàng đến thế.\r\n+ Linh hồn của món Pizza và Gà cay: Nhờ được làm từ 100% sữa bò tươi nguyên chất, phô mai Mozzarella của chúng tôi có độ ẩm hoàn hảo, tan chảy đều khi gặp nhiệt độ cao, tạo nên lớp phủ béo ngậy, kéo sợi dài dai vô cùng kích thích vị giác.\r\n+ Làm phô mai que giòn rụm: Chỉ cần cắt phô mai thành từng thanh, lăn qua bột chiên xù và chiên ngập dầu. Lớp vỏ ngoài giòn rụm ôm trọn nhân phô mai dẻo thơm bên trong sẽ khiến cả gia đình thích mê.\r\nMẹo bảo quản để phô mai luôn tươi ngon:\r\nĐể giữ được độ dẻo dai và hương vị thơm béo tốt nhất, hãy luôn bảo quản phô mai Mozzarella trong ngăn mát tủ lạnh ở nhiệt độ lý tưởng từ 2-8°C. Sản phẩm có hạn sử dụng lên đến 6 tháng, giúp bạn luôn sẵn sàng sáng tạo các món ngon bất cứ lúc nào!', 0, '2026-06-02 00:35:56'),
(4, 'Nâng Tầm Bữa Sáng Nhanh Gọn Cùng Phô Mai Cheddar Cắt Lát', 'nang-tam-bua-sang-nhanh-gon-cung-pho-mai-cheddar-cat-lat', 'post_1780274295.jpg', 'Biến chiếc bánh mì sandwich hay hamburger tẻ nhạt thành cực phẩm hương vị chỉ với một lát phô mai Cheddar béo ngậy, giàu dinh dưỡng.', 'Sáng thức dậy muộn nhưng vẫn muốn một bữa ăn chất lượng? Bí quyết nằm ở những lát Phô mai Cheddar vàng ươm, thơm lừng của Phô Mai 3 Anh Em. Không cần chế biến cầu kỳ, sản phẩm này chính là \"vũ khí bí mật\" giúp món ăn của bạn thăng hạng hương vị trong tích tắc.\r\n\r\n+ Hoàn hảo cho Hamburger & Sandwich: Cheddar có vị đậm đà đặc trưng, hơi mặn nhẹ và vô cùng đưa miệng. Chỉ cần kẹp một lát phô mai vào giữa lớp thịt nướng nóng hổi và bánh mì mềm, hơi nóng sẽ làm phô mai tan chảy nhẹ, quyện vào từng thớ thịt.\r\n+ Thăng hoa cùng món súp: Bạn đã bao giờ thử thả một lát Cheddar vào bát súp bí đỏ hay súp kem nấm ấm nóng chưa? Hương vị mặn mòi, béo ngậy của Cheddar làm từ 100% sữa bò tươi nguyên chất sẽ khiến món súp thêm phần đặc sánh và hấp dẫn.\r\n\r\nLưu ý khi sử dụng:\r\nSản phẩm được thiết kế dạng cắt lát mỏng cực kỳ tiện dụng. Sau khi bóc vỏ, hãy bọc kín phần chưa sử dụng và cất vào ngăn mát tủ lạnh (2-8°C) để đảm bảo chất lượng trong suốt 6 tháng hạn sử dụng nhé.', 1, '2026-06-03 00:38:15'),
(5, 'Bữa Phụ Tiện Lợi, Giàu Canxi Cho Bé Với Phô Mai Viên \"3 Anh Em\"', 'bua-phu-tien-loi-giau-canxi-cho-be-voi-pho-mai-vien-3-anh-em', 'post_1780274361.jpg', 'Giải pháp ăn vặt vừa ngon miệng vừa bổ dưỡng, cung cấp dồi dào canxi mỗi ngày cho cả gia đình với những viên phô mai béo ngậy, mềm mịn.', 'Trẻ nhỏ thường lười uống sữa nhưng lại rất khó chối từ những viên phô mai mềm mịn, thơm lừng. Hiểu được tâm lý đó, Phô Mai 3 Anh Em mang đến dòng Phô mai dạng viên/tam giác, định vị là nguồn \"Dinh dưỡng cho mọi nhà\".\r\n\r\n+ Ăn vặt siêu tiện lợi: Được chia thành từng khẩu phần nhỏ gọn, phô mai viên rất tiện để mang theo đi học, đi làm hay dùng làm bữa phụ nạp năng lượng buổi xế chiều. Chỉ cần bóc lớp giấy bạc là có thể thưởng thức ngay.\r\n+ Nguồn bổ sung Canxi tuyệt vời: Được cô đặc từ 100% sữa bò tươi nguyên chất, mỗi viên phô mai chứa hàm lượng canxi và protein dồi dào, hỗ trợ sự phát triển xương chắc khỏe cho trẻ em và bổ sung dinh dưỡng thiết yếu cho người lớn tuổi.\r\n+ Đa dạng cách dùng: Ngoài việc ăn trực tiếp, bạn có thể phết phô mai tam giác lên bánh mì giòn, trộn cùng salad trái cây, hoặc dằm nhuyễn nấu cùng cháo dinh dưỡng cho các bé ăn dặm.\r\n\r\nSản phẩm được sản xuất theo quy trình khép kín, giữ trọn hương vị chuẩn Âu. Hãy bảo quản phô mai trong ngăn mát (2-8°C) để viên phô mai luôn giữ được kết cấu mềm dẻo hoàn hảo trong suốt 6 tháng nhé.', 0, '2026-06-04 00:39:21'),
(6, 'Phô mai', 'pho-mai', 'post_1780878772.jpg', 'Phô mai 3 anh em', 'Bữa sáng với phô mai', 0, '2026-06-08 00:32:52'),
(7, 'Mon ăn với phô mai', 'mon-an-voi-pho-mai', 'post_1780878909.jpg', 'Món ăn ngon, nhẹ nhàng bằng phô mai', 'Sáng thức dậy muộn nhưng vẫn muốn một bữa ăn chất lượng? Bí quyết nằm ở những lát Phô mai  vàng ươm, thơm lừng của Phô Mai 3 Anh Em. Không cần chế biến cầu kỳ, sản phẩm này chính là \"vũ khí bí mật\" giúp món ăn của bạn thăng hạng hương vị trong tích tắc.\r\n\r\n+ Hoàn hảo cho Hamburger & Sandwich: Cheddar có vị đậm đà đặc trưng, hơi mặn nhẹ và vô cùng đưa miệng. Chỉ cần kẹp một lát phô mai vào giữa lớp thịt nướng nóng hổi và bánh mì mềm, hơi nóng sẽ làm phô mai tan chảy nhẹ, quyện vào từng thớ thịt.\r\n+ Thăng hoa cùng món súp: Bạn đã bao giờ thử thả một lát Cheddar vào bát súp bí đỏ hay súp kem nấm ấm nóng chưa? Hương vị mặn mòi, béo ngậy của Cheddar làm từ 100% sữa bò tươi nguyên chất sẽ khiến món súp thêm phần đặc sánh và hấp dẫn.\r\n\r\nLưu ý khi sử dụng:\r\nSản phẩm được thiết kế dạng cắt lát mỏng cực kỳ tiện dụng. Sau khi bóc vỏ, hãy bọc kín phần chưa sử dụng và cất vào ngăn mát tủ lạnh (2-8°C) để đảm bảo chất lượng trong suốt 6 tháng hạn sử dụng nhé.', 0, '2026-06-08 00:35:09');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `description` text DEFAULT NULL,
  `short_desc` varchar(300) DEFAULT NULL,
  `price` decimal(12,0) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `original_price` decimal(12,0) DEFAULT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `weight_gram` int(10) UNSIGNED DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_on_sale` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_desc`, `price`, `sale_price`, `original_price`, `stock`, `weight_gram`, `origin`, `image`, `is_featured`, `is_on_sale`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Parmesan Reggiano 24 Tháng', 'parmesan-reggiano-24-thang', 'Parmigiano-Reggiano là \"vua của các loại phô mai\" đến từ vùng Emilia-Romagna, Ý. Được ủ tối thiểu 24 tháng trong hầm đá truyền thống, phô mai có kết cấu hạt mịn đặc trưng, tan chảy trong miệng với hậu vị ngọt nhẹ, bơ béo và nấm umami sâu. Đây là loại phô mai bắt buộc phải có trong bếp của mọi người yêu ẩm thực.', 'Vua phô mai Ý, ủ 24 tháng — kết cấu hạt, hương vị umami sâu.', 685000, NULL, 750000, 50, 200, 'Ý (Emilia-Romagna)', 'parmesan-reggiano.jpg', 1, 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(2, 1, 'Cheddar Vintage 18 Tháng', 'cheddar-vintage-18-thang', 'Cheddar Vintage từ vùng Somerset, Anh — ủ trong 18 tháng để đạt được vị chua nhẹ đặc trưng, hương bơ đậm, kết cấu cứng dẻo với những tinh thể muối nhỏ li ti. Tuyệt vời khi dùng kèm táo xanh, hạt óc chó hoặc nhai cùng bánh mì nướng.', 'Cheddar Anh ủ 18 tháng — béo ngậy, tinh thể muối, vị chua dịu.', 420000, NULL, 480000, 80, 250, 'Anh (Somerset)', 'cheddar-vintage.jpg', 1, 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(3, 1, 'Gruyère AOP Thụy Sĩ', 'gruyere-aop-thuy-si', 'Gruyère AOP là phô mai Thụy Sĩ chính gốc được chứng nhận bảo hộ địa lý. Ủ trong hầm đá 12–18 tháng, phô mai có kết cấu đặc, không có lỗ hổng, hương vị phức hợp gồm bơ, hạt, và một chút trái cây ngọt. Đây là nguyên liệu quan trọng trong món Fondue và French Onion Soup.', 'Phô mai Thụy Sĩ AOP — kết cấu đặc, hương bơ và hạt, hoàn hảo cho Fondue.', 520000, NULL, NULL, 60, 200, 'Thụy Sĩ (Fribourg)', 'gruyere-aop.jpg', 0, 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(4, 1, 'Manchego Curado 6 Tháng', 'manchego-curado-6-thang', 'Manchego Curado được làm từ sữa cừu Manchega thuần chủng ở vùng La Mancha, Tây Ban Nha. Ủ trong 6 tháng với lớp vỏ đặc trưng hình zigzag, phô mai có kết cấu cứng vừa, hương vị đậm đà, béo, với nốt thảo mộc và caramel nhẹ. Hoàn hảo với quince paste và rượu vang đỏ.', 'Phô mai cừu Tây Ban Nha — kết cấu cứng, hương caramel và thảo mộc.', 395000, NULL, 430000, 45, 200, 'Tây Ban Nha (La Mancha)', 'manchego-curado.jpg', 0, 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(5, 2, 'Mozzarella di Bufala Campana', 'mozzarella-di-bufala-campana', 'Mozzarella di Bufala Campana DOP — làm từ sữa trâu nước 100% tại vùng Campania, Ý. Kết cấu mềm dẻo co giãn đặc trưng, vị sữa tươi béo ngậy, khi cắt ra có nước whey trong chảy ra. Dùng ăn sống với cà chua, dầu olive và húng quế — hoàn hảo tuyệt đối cho món Caprese.', 'Mozzarella trâu Ý DOP — mềm dẻo, sữa tươi, hoàn hảo cho Caprese.', 280000, NULL, NULL, 100, 125, 'Ý (Campania)', 'mozzarella-bufala.jpg', 1, 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(6, 2, 'Burrata Fresca Puglia', 'burrata-fresca-puglia', 'Burrata là \"túi\" Mozzarella bao bọc bên ngoài, bên trong chứa hỗn hợp kem tươi và sợi Stracciatella. Cắt ra, kem béo ngậy chảy ra mềm mại — đây là trải nghiệm phô mai xa hoa nhất. Nên ăn trong vòng 48 giờ sau sản xuất để cảm nhận độ tươi tuyệt hảo.', 'Burrata Puglia — vỏ Mozzarella, nhân kem chảy, xa hoa và tươi ngon.', 320000, NULL, NULL, 60, 150, 'Ý (Puglia)', 'burrata-puglia.jpg', 1, 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(7, 2, 'Ricotta Fresca Italiana', 'ricotta-fresca-italiana', 'Ricotta (nghĩa đen: \"nấu lại\") được làm từ whey còn sót lại sau khi sản xuất các phô mai khác. Kết cấu xốp nhẹ, kem trắng tinh, vị ngọt thanh, ít mặn. Đa năng trong bếp: dùng làm nhân ravioli, bánh cheesecake, hoặc phết lên bánh mì nướng cùng mật ong.', 'Ricotta Ý tươi — xốp nhẹ, thanh ngọt, đa năng trong nấu ăn.', 180000, NULL, NULL, 70, 250, 'Ý', 'ricotta-fresca.jpg', 0, 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(8, 2, 'Cream Cheese Philadelphia Style', 'cream-cheese-philadelphia-style', 'Cream Cheese mịn màng, béo ngậy — được làm theo phong cách Philadelphia với hàm lượng kem cao. Kết cấu phết dễ dàng ngay khi lấy ra từ tủ lạnh. Hoàn hảo cho cheesecake, bagel hoặc làm frosting cho bánh.', 'Cream Cheese kem cao cấp — mịn màng, béo nhẹ, hoàn hảo cho cheesecake.', 145000, NULL, 160000, 120, 200, 'Nhập khẩu (Đan Mạch)', 'cream-cheese.jpg', 0, 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(9, 3, 'Gorgonzola Piccante DOP', 'gorgonzola-piccante-dop', 'Gorgonzola Piccante là phô mai xanh nổi tiếng nhất nước Ý, có chứng nhận DOP. Ủ trong 6–12 tháng, vân xanh lam chạy dọc khắp phô mai từ nấm Penicillium glaucum. Hương vị mạnh mẽ, cay nồng, mặn đậm với hậu vị peppery. Hoàn hảo với mật ong hoa cam, quả lê và Prosecco.', 'Gorgonzola Ý DOP — vân xanh, hương nồng, hoàn hảo với mật ong.', 490000, NULL, 540000, 40, 200, 'Ý (Lombardia/Piemonte)', 'gorgonzola-piccante.jpg', 1, 0, 1, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(10, 3, 'Roquefort AOP Pháp', 'roquefort-aop-phap', 'Roquefort — \"vua của các loại phô mai xanh\" — được làm từ sữa cừu và ủ trong hang đá vôi tự nhiên ở Combalou, miền Nam nước Pháp. Phô mai có màu ngà trắng với vân xanh đặc trưng, kết cấu mềm ẩm, hương vị cay, mặn, nồng và phức hợp tuyệt vời.', 'Vua phô mai xanh Pháp AOP — sữa cừu, hang đá, hương vị phức hợp.', 580000, NULL, NULL, 30, 150, 'Pháp (Aveyron)', 'roquefort-aop.jpg', 0, 0, 1, '2026-05-25 15:19:14', '2026-05-28 14:47:20'),
(11, 3, 'Stilton Blue Cheese PDO', 'stilton-blue-cheese-pdo', '-Đặc điểm hương vị và kết cấu:\r\n+Đậm đà, béo ngậy vị kem, xen lẫn vị mặn, hương hạt dẻ tinh tế và một chút cay nồng đặc trưng từ nấm mốc Penicillium roqueforti.\r\n+Kết cấu: Mềm mịn như kem nhưng lại dễ vụn, tạo cảm giác tan ngay trong miệng.\r\n+Thời gian ủ: Thường được ủ chín tối thiểu từ 9 tuần trở lên để đạt được hương vị chuẩn mực. \r\n\r\n-Cách thưởng thức phổ biến:\r\n+Ăn trực tiếp: Kết hợp hoàn hảo với các loại trái cây ngọt (lê, táo, sung ngọt), mật ong và các loại hạt như hạt óc chó trên một khay phô mai (cheeseboard).\r\n+Kết hợp đồ uống: Thường được nhâm nhi cùng rượu Port (rượu vang cường hóa) hoặc rượu vang đỏ đậm đà.\r\n+Chế biến món ăn: Bóp vụn để trộn salad, làm nước xốt cho các món bít tết, hoặc thêm vào mì Ý (pasta) và cơm Risotto để tăng độ béo\r\n', 'Stilton là phô mai xanh danh tiếng của Anh, được bảo hộ địa lý PDO — chỉ được sản xuất tại 3 hạt Derbyshire, Leicestershire và Nottinghamshire.', 510000, NULL, 560000, 200, 200, 'Anh (Leicestershire)', 'stilton-pdo.jpg', 0, 0, 1, '2026-05-25 15:19:14', '2026-06-08 07:50:58'),
(12, 4, 'Gouda Aged 12 Tháng', 'gouda-aged-12-thang', 'Gouda Aged từ Hà Lan — ủ 12 tháng để đạt màu vàng caramel đặc trưng bên trong, kết cấu dẻo nhưng có tinh thể muối nhỏ. Hương vị ngọt, caramel, bơ và hạt phức hợp. Đây là phiên bản cao cấp hơn nhiều so với Gouda thông thường bán ở siêu thị.', 'Gouda Hà Lan ủ 12 tháng — caramel ngọt, tinh thể muối, dẻo thơm.', 360000, NULL, 400000, 71, 250, 'Hà Lan', 'gouda-aged.jpg', 0, 0, 1, '2026-05-25 15:19:14', '2026-06-10 13:03:33'),
(13, 4, 'Emmental Grand Cru', 'emmental-grand-cru', 'Emmental Grand Cru là phô mai Thụy Sĩ nổi tiếng với những lỗ hổng tròn lớn đặc trưng (do khí CO₂ từ vi khuẩn tạo ra trong quá trình ủ). Hương vị nhẹ nhàng, ngọt, bơ và hạt. Tan chảy tuyệt hảo — là nguyên liệu cổ điển cho sandwich nóng, Croque Monsieur và Fondue.', 'Emmental Thụy Sĩ Grand Cru — lỗ tròn đặc trưng, ngọt nhẹ, tan chảy tuyệt hảo.', 340000, NULL, NULL, 146, 250, 'Thụy Sĩ', 'emmental-grand-cru.jpg', 0, 0, 1, '2026-05-25 15:19:14', '2026-06-10 10:29:58'),
(14, 4, 'Brie de Meaux AOP', 'brie-de-meaux-aop', 'Brie de Meaux — \"nữ hoàng phô mai Pháp\" — là phô mai mềm với lớp vỏ trắng mốc mịn như nhung (Penicillium camemberti). Bên trong mềm chảy kem ở nhiệt độ phòng, hương vị nhẹ nhàng, bơ béo, nấm đất và một chút amoniac. Dùng cùng bánh baguette, nho xanh và champagne.', 'Brie de Meaux AOP là loại phô mai mềm làm từ sữa bò thô nguyên chất, nổi tiếng với danh hiệu \"Vua của các loại phô mai\" (Le Roi des Fromages) được phong tặng tại Hội nghị Vienna năm 1815. Chữ AOP (Appellation d\'Origine Protégée - Chỉ dẫn địa lý được bảo hộ của Châu Âu) đảm bảo phô mai chỉ được sản x', 460000, 450000.00, 500000, 38, 200, 'Pháp (Île-de-France)', 'brie-de-meaux.jpg', 1, 1, 1, '2026-05-25 15:19:14', '2026-06-08 07:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `status` varchar(20) DEFAULT 'inactive',
  `activation_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `role`, `status`, `activation_token`, `reset_token`, `token_expire`, `created_at`, `updated_at`) VALUES
(1, 'Quản Trị Viên', 'admin@phomai3anhem.vn', '$2y$12$RoMYzE0jEYpKaTHDqU9OUuXDAPiL3X5Kph.u7aSyVx1pQf2u9Tz2K', '0901234567', NULL, 'admin', 'inactive', NULL, NULL, NULL, '2026-05-25 15:19:14', '2026-05-25 15:19:14'),
(4, 'Tung', 'Tung@gmail.com', '$2y$10$uB7Eo2.IKy7GTfZ3bFxYCOppSSayenfRz.fT4SAHH35ziy41awFlO', '', '', 'admin', 'active', NULL, NULL, NULL, '2026-05-27 07:19:04', '2026-06-04 09:45:01'),
(5, 'Binh', 'Binh@gmail.com', '$2y$10$ulnn2zx4bzlcQVCTJ.6jzeWZE3JbrzEtn7M0dTEKpLaZ6lx8hfJNO', '098123456', '', 'customer', 'inactive', NULL, NULL, NULL, '2026-05-28 07:46:46', '2026-05-28 07:46:46'),
(6, 'TTT', 'TTT@gmail.com', '$2y$10$lfyYpRaspu4MATE9BHM4D.yZ5twHl7kxrnS722BygziksefdBT.b6', '090123456', '', 'customer', 'inactive', NULL, NULL, NULL, '2026-05-28 08:09:30', '2026-05-28 08:09:30'),
(9, 'Khải', 'khaicc67@gmail.com', '$2y$10$9Go.Nx3yGkD.iDLv1OieTuIS2Cdt2XJ.ReXwI1YuJBiV2sXguTDsW', '090123456', 'd73', 'customer', 'inactive', NULL, NULL, NULL, '2026-06-01 14:37:33', '2026-06-01 14:37:49'),
(11, 'khang67', 'minhkhangt2812@gmail.com', '$2y$10$qIFvvlExS.gjW3UM7tZjyOlQeZFFa0aoBr6U1wQfV9xWHo70SwN86', '1234567890', '1', 'customer', 'inactive', NULL, NULL, NULL, '2026-06-04 09:07:34', '2026-06-04 09:07:34'),
(12, 'khai22', 'duykhain4@gmail.com', '$2y$10$ATrxStL9yYdUxpoj2D2ZE.lYLGyNEdvuEldNUA7cGEr2RlpPKKg6C', '1234567890', '132', 'customer', 'active', NULL, NULL, NULL, '2026-06-04 09:15:53', '2026-06-04 09:19:30'),
(13, 'khainguyen', 'nduykha805@gmail.com', '$2y$10$FfX/CNHEmbzNEyfqeWFdsOF.RMPAdunWGe9uTz8KeDNIyLj6paKyK', '0901234568', 'tutu', 'customer', 'inactive', 'd5c073e0ea0c00b0cbacc7805cae01faffcfa1479dcfdf863de9d129e177f936', NULL, NULL, '2026-06-08 08:49:59', '2026-06-08 08:49:59'),
(14, 'khai', 'nduykhai805@gmail.com', '$2y$10$4mKDBWMoc1dybkFKPSHYpubIXP97iSzcA03dGVaGaxPSCzMfzVLeG', '0901234568', 'tutu', 'customer', 'active', NULL, NULL, NULL, '2026-06-08 08:52:44', '2026-06-08 08:53:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_price` (`price`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_featured` (`is_featured`);
ALTER TABLE `products` ADD FULLTEXT KEY `idx_search` (`name`,`description`,`short_desc`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
