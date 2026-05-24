<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_text(trim($_POST['email'] ?? ''));

    if (validate_email($email)) {
        try {
            // Tự động tạo bảng nếu chưa có
            $pdo->exec("CREATE TABLE IF NOT EXISTS subscribers (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(100) UNIQUE NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
            
            $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $stmt->execute([$email]);
            
            echo "<script>alert('Cảm ơn bạn đã đăng ký nhận tin từ ShoeStore!'); window.location.href='index.php';</script>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "<script>alert('Email này đã được đăng ký trước đó!'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại sau!'); window.location.href='index.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Vui lòng nhập email hợp lệ!'); window.location.href='index.php';</script>";
    }
} else {
    redirect('index.php');
}
?>
