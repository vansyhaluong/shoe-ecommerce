<?php 
$title = 'Đăng nhập - ShoeStore';
include 'header.php'; 

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role']; // Đảm bảo lưu role để vào admin

        $url = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        redirect($url);
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    }
}
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-sm border">
        <h2 class="text-3xl font-bold mb-6 text-center">Đăng nhập</h2>
        <?php if($error): ?>
            <p class="text-red-500 mb-4 text-center"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 font-bold">Tên đăng nhập</label>
                <input type="text" name="username" required class="w-full border rounded-lg px-3 py-2" placeholder="Nhập tên đăng nhập...">
            </div>
            <div>
                <label class="block mb-1 font-bold">Mật khẩu</label>
                <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2">
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition">Đăng nhập</button>
            <p class="text-center text-sm text-gray-500">Chưa có tài khoản? <a href="register.php" class="text-primary hover:underline">Đăng ký ngay</a></p>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
