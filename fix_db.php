<?php
include 'config.php';

try {
    // Thêm cột username vào bảng users
    $pdo->exec("ALTER TABLE users ADD COLUMN username VARCHAR(50) UNIQUE AFTER name");
    echo "<h1 style='color:green'>Thành công! Cột 'username' đã được thêm vào bảng users.</h1>";
    echo "<p>Bây giờ bạn có thể quay lại trang <a href='register.php'>Đăng ký</a>.</p>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "<h1 style='color:blue'>Cột 'username' đã tồn tại từ trước rồi!</h1>";
        echo "<p>Quay lại trang <a href='register.php'>Đăng ký</a>.</p>";
    } else {
        echo "<h1 style='color:red'>Lỗi: " . $e->getMessage() . "</h1>";
    }
}
?>
