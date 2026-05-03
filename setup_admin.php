<?php
include 'config.php';

try {
    $username = 'admin';
    $password = password_hash('123456', PASSWORD_BCRYPT);
    $email = 'admin@shoestore.com';
    $name = 'Quản trị viên';

    // Xóa admin cũ nếu trùng username
    $pdo->prepare("DELETE FROM users WHERE username = ?")->execute([$username]);

    $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, 'admin')");
    $stmt->execute([$name, $username, $email, $password]);

    echo "<h1 style='color:green'>Tạo tài khoản Admin thành công!</h1>";
    echo "<p>Username: <b>admin</b></p>";
    echo "<p>Password: <b>123456</b></p>";
    echo "<p><a href='login.php'>Đi tới trang Đăng nhập</a></p>";
} catch (Exception $e) {
    echo "<h1 style='color:red'>Lỗi: " . $e->getMessage() . "</h1>";
}
?>
