<?php
include 'config.php';

try {
    // Create wishlist table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `wishlist` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `product_id` int(11) NOT NULL,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
          FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
          UNIQUE KEY `unique_user_product` (`user_id`, `product_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "<p style='color:green'>Wishlist table created successfully!</p>";
    echo "<h1 style='color:green'>Migration complete!</h1>";
} catch (Exception $e) {
    echo "<h1 style='color:red'>Error: " . $e->getMessage() . "</h1>";
}
?>
