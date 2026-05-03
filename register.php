<?php 
$title = 'Đăng ký - ShoeStore';
include 'header.php'; 

// Tự động kiểm tra và thêm cột username nếu chưa có
try {
    $pdo->query("SELECT username FROM users LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("ALTER TABLE users ADD COLUMN username VARCHAR(50) UNIQUE AFTER name");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } else {
        // Kiểm tra username hoặc email đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Tên đăng nhập hoặc Email đã tồn tại!';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, 'user')");
            if ($stmt->execute([$name, $username, $email, $hashedPassword])) {
                redirect('login.php');
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại!';
            }
        }
    }
}
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-sm border">
        <h2 class="text-3xl font-bold mb-6 text-center">Đăng ký tài khoản</h2>
        <?php if($error): ?>
            <p class="text-red-500 mb-4 text-center"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 font-bold">Họ và tên</label>
                <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2" placeholder="VD: Nguyễn Văn A">
            </div>
            <div>
                <label class="block mb-1 font-bold">Tên đăng nhập</label>
                <input type="text" name="username" required class="w-full border rounded-lg px-3 py-2" placeholder="VD: van_a_99">
            </div>
            <div>
                <label class="block mb-1 font-bold">Email</label>
                <input type="email" name="email" required class="w-full border rounded-lg px-3 py-2" placeholder="email@vi-du.com">
            </div>
            <div>
                <label class="block mb-1 font-bold">Mật khẩu</label>
                <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block mb-1 font-bold">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition">Đăng ký ngay</button>
            <p class="text-center text-sm text-gray-500">Đã có tài khoản? <a href="login.php" class="text-primary hover:underline">Đăng nhập</a></p>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
