-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 22, 2026 lúc 04:04 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shopgiay`
--
CREATE DATABASE IF NOT EXISTS `shopgiay` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `shopgiay`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `created_at`) VALUES
(1, 'Nike', '2026-05-03 02:02:25'),
(2, 'Adidas', '2026-05-03 02:02:25'),
(3, 'Puma', '2026-05-03 02:02:25'),
(4, 'Vans', '2026-05-03 02:02:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Men', 'men', NULL, '2026-05-03 02:02:25'),
(2, 'Unisex', 'unisex', NULL, '2026-05-03 02:02:25'),
(3, 'Women', 'women', NULL, '2026-05-03 02:02:25'),
(4, 'Sport', 'sport', NULL, '2026-05-03 02:02:25'),
(5, 'Dây giày', 'shoelaces', 'Phụ kiện dây giày chất lượng cao cho sneaker.', '2026-05-22 07:55:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `is_from_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `session_id`, `message`, `is_from_admin`, `created_at`) VALUES
(1, 3, 'cabser8dacj2nphofk73mplqhf', 'giày nike còn hàng ko', 0, '2026-04-29 09:34:29'),
(2, 3, 'cabser8dacj2nphofk73mplqhf', 'giày nike còn hàng ko', 0, '2026-04-29 09:36:16'),
(3, 3, 'cabser8dacj2nphofk73mplqhf', 'Cảm ơn bạn đã quan tâm! Tin nhắn của bạn đã được chuyển đến nhân viên hỗ trợ. Chúng mình sẽ phản hồi bạn trong ít phút nhé! 🙏', 1, '2026-04-29 09:36:16'),
(4, 3, 'd4c1mo8hsd718is5v28i60jnof', 'Chọn size', 0, '2026-05-22 09:08:59'),
(5, 3, 'd4c1mo8hsd718is5v28i60jnof', 'Chọn size cực kỳ đơn giản ngay tại trang chi tiết sản phẩm. Shop khuyên bạn nên chọn đúng size (true-to-size) thường ngày của mình nhé!', 1, '2026-05-22 09:08:59'),
(6, 3, 'd4c1mo8hsd718is5v28i60jnof', 'Giao hàng', 0, '2026-05-22 09:09:05'),
(7, 3, 'd4c1mo8hsd718is5v28i60jnof', 'Đơn hàng của bạn sẽ được vận chuyển nhanh chóng từ 2-5 ngày làm việc. Đặc biệt shop hỗ trợ thanh toán COD khi nhận hàng!', 1, '2026-05-22 09:09:05'),
(8, 3, 'd4c1mo8hsd718is5v28i60jnof', 'giày nike còn hàng ko', 0, '2026-05-22 09:09:13'),
(9, 3, 'd4c1mo8hsd718is5v28i60jnof', 'Bạn có thể xem các mẫu sneaker mới nhất tại trang Sản phẩm. Shop có nhiều mẫu giày theo phong cách Nike + GOAT.', 1, '2026-05-22 09:09:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending',
  `payment_method` enum('cod','online') DEFAULT 'cod',
  `shipping_name` varchar(100) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `shipping_name`, `shipping_phone`, `shipping_address`, `created_at`) VALUES
(11, NULL, 6300000.00, 'pending', 'cod', 'văn sỹ hà lương', '00222222', 'aaaa', '2026-04-29 08:02:55'),
(12, 3, 3500000.00, 'pending', 'cod', 'văn sỹ hà lương', '00222222', 'ffff', '2026-04-29 08:24:50'),
(13, 3, 5800000.00, 'pending', 'cod', 'văn sỹ hà lương', '00222222', 'lllll', '2026-04-29 08:57:35'),
(14, 3, 3500000.00, 'pending', 'cod', 'văn sỹ hà lương', '03444', 'lê văn việt', '2026-05-03 02:43:48'),
(16, 3, 3500000.00, 'completed', 'cod', 'văn sỹ hà lương', '00222222', '487 phạm văn đồng', '2026-05-22 08:30:12'),
(17, 3, 4200000.00, 'completed', 'cod', 'văn sỹ hà lương', '48855', 'cvv', '2026-05-22 09:34:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `size`) VALUES
(5, 11, 3, 1, 2800000.00, NULL),
(6, 11, 1, 1, 3500000.00, NULL),
(7, 12, 1, 1, 3500000.00, NULL),
(8, 13, 5, 2, 2900000.00, NULL),
(9, 14, 1, 1, 3500000.00, NULL),
(12, 16, 1, 1, 3500000.00, 40),
(13, 17, 2, 1, 4200000.00, 40);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `brand_id`, `category_id`, `name`, `description`, `price`, `brand`, `category`, `created_at`) VALUES
(1, 1, 1, 'Nike Air Max 270', 'Giày thể thao êm ái, phong cách hiện đại.', 3500000.00, 'Nike', 'Men', '2026-04-29 05:28:39'),
(2, 2, 1, 'Adidas Ultraboost 22', 'Giày chạy bộ chuyên nghiệp, siêu nhẹ.', 4200000.00, 'Adidas', 'Men', '2026-04-29 05:28:39'),
(3, 3, 2, 'Puma RS-X3', 'Thiết kế retro cực chất, màu sắc trẻ trung.', 2800000.00, 'Puma', 'Unisex', '2026-04-29 05:28:39'),
(4, 4, 2, 'Vans Old Skool', 'Mẫu giày classic không bao giờ lỗi thời.', 1500000.00, 'Vans', 'Unisex', '2026-04-29 05:28:39'),
(5, 1, 3, 'Nike Air Force 1', 'Biểu tượng của văn hóa sneaker toàn cầu.', 2900000.00, 'Nike', 'Women', '2026-04-29 05:28:39'),
(7, 1, 4, 'Air Max Plus', 'Giày thể thao Nike giúp hoạt động thoải mái trong mùa ', 27000.00, NULL, NULL, '2026-05-03 02:22:36'),
(8, NULL, 5, 'Dây giày trắng basic', 'Dây giày dẹt trắng basic chất liệu cotton cao cấp 100%, bền màu, phù hợp mọi loại sneaker huyền thoại như Air Force 1, Superstar.', 25000.00, 'Phụ kiện', 'Dây giày', '2026-05-22 07:55:23'),
(9, NULL, 5, 'Dây giày đen basic', 'Dây giày dẹt đen basic dệt kép chắc chắn, mang lại vẻ ngoài cá tính và hạn chế bám bẩn cực tốt cho đôi giày của bạn.', 25000.00, 'Phụ kiện', 'Dây giày', '2026-05-22 07:55:23'),
(10, NULL, 5, 'Dây giày phản quang', 'Dây giày phản quang 3M nổi bật và phát sáng độc đáo trong đêm. Chất liệu co giãn đàn hồi tốt và bền bỉ.', 45000.00, 'Phụ kiện', 'Dây giày', '2026-05-22 07:55:23'),
(11, NULL, 5, 'Dây giày oval thể thao', 'Dây giày dạng dẹt tròn/oval thể thao chuyên dụng cho các hoạt động chạy bộ, tập gym, giảm lực cản gió và không bị tuột nút thắt.', 35000.00, 'Phụ kiện', 'Dây giày', '2026-05-22 07:55:23'),
(12, NULL, 5, 'Dây giày vintage cream', 'Dây giày tone màu kem ngả vàng cổ điển cực chất. Hoàn hảo để custom đôi sneaker của bạn sang phong cách Vintage/Neo-retro.', 30000.00, 'Phụ kiện', 'Dây giày', '2026-05-22 07:55:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_primary`) VALUES
(1, 1, '/images/prod_69f6aed6946e17.99332232.jpg', 1),
(2, 2, '/images/prod_69f6add907b614.29013045.webp', 1),
(3, 3, '/images/prod_69f6af2c7cb715.51250911.webp', 1),
(4, 4, '/images/prod_69f6af5a9fd080.17834197.jpg', 1),
(5, 5, '', 1),
(7, 7, '/images/prod_69f6b16c8f15a8.48076168.avif', 1),
(8, 7, '/images/prod_69f6b4b868ea96.19962494.webp', 0),
(9, 8, '/images/shoelaces_white.png', 1),
(10, 9, '/images/shoelaces_black.png', 1),
(11, 10, '/images/shoelaces_reflective.png', 1),
(12, 11, '/images/shoelaces_oval.png', 1),
(13, 12, '/images/shoelaces_vintage.png', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reviewer_name` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `user_id`, `reviewer_name`, `rating`, `comment`, `created_at`) VALUES
(1, 7, NULL, 'văn sỹ hà lương', 5, 'sản phẩm xấu quas', '2026-05-22 08:54:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `return_requests`
--

CREATE TABLE `return_requests` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `return_requests`
--

INSERT INTO `return_requests` (`id`, `order_id`, `user_id`, `reason`, `status`, `admin_note`, `created_at`) VALUES
(1, 16, 3, 'size nhỏ quá', 'approved', 'ok', '2026-05-22 09:20:31'),
(2, 17, 3, 'giày xấu', 'rejected', 'không chấp nhận', '2026-05-22 09:35:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `phone`, `address`, `created_at`) VALUES
(3, 'văn sỹ hà lương', 'vansyhaluong', 'vansyhaluong08@gmail.comva', '$2y$10$ZbricN9siJjHQBJKCmSJdeHDTnp/sqlEMM8utJM0/AaCag.mTRza.', 'user', NULL, NULL, '2026-04-29 08:10:36'),
(4, 'Quản trị viên', 'admin', 'admin@shoestore.com', '$2y$10$idQJ3IsF.jHyiPhSLvej/uNEmhPVKyCqcommdVQom3xGEIvqPU8/6', 'admin', NULL, NULL, '2026-04-29 08:31:16'),
(7, 'user', 'user', 'user@gmail.com', '123', 'user', '022245', 'aaaaaaaaa', '2026-05-22 13:59:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 3, 2, '2026-05-22 07:30:47'),
(2, 3, 3, '2026-05-22 07:30:52'),
(3, 3, 8, '2026-05-22 07:56:52');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `return_requests`
--
ALTER TABLE `return_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_order_return` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `return_requests`
--
ALTER TABLE `return_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `return_requests`
--
ALTER TABLE `return_requests`
  ADD CONSTRAINT `return_requests_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `return_requests_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
