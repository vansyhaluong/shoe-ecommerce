<?php
include 'config.php';

try {
    // 1. Create product_reviews table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `product_reviews` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `product_id` int(11) NOT NULL,
          `user_id` int(11) DEFAULT NULL,
          `reviewer_name` varchar(255) NOT NULL,
          `rating` int(11) NOT NULL,
          `comment` text NOT NULL,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
          FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "<p style='color:green'>product_reviews table created successfully!</p>";

    // 2. Add size column to order_items
    try {
        $pdo->exec("ALTER TABLE `order_items` ADD COLUMN `size` int(11) DEFAULT NULL");
        echo "<p style='color:green'>size column added to order_items successfully!</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p style='color:blue'>size column already exists in order_items.</p>";
        } else {
            throw $e;
        }
    }

    echo "<h1 style='color:green'>Migration complete!</h1>";
} catch (Exception $e) {
    echo "<h1 style='color:red'>Error: " . $e->getMessage() . "</h1>";
}
?>
