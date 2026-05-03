<?php
include 'config.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) DEFAULT NULL,
      `session_id` varchar(100) DEFAULT NULL,
      `message` text NOT NULL,
      `is_from_admin` tinyint(1) DEFAULT 0,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "<h1>Kích hoạt hệ thống Chat thành công!</h1><a href='index.php'>Quay về trang chủ</a>";
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
