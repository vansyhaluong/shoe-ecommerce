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

<div class="container mx-auto px-4 py-16 flex items-center justify-center min-h-[calc(100vh-200px)]">
    <div class="w-full max-w-lg bg-white p-10 md:p-12 rounded-[2.5rem] shadow-2xl border border-slate-100 hover:shadow-indigo-500/5 transition-all duration-500">
        <div class="text-center mb-10">
            <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-4 py-1.5 rounded-full uppercase tracking-[0.15em] mb-4 inline-block font-display">Welcome Back</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-dark mb-3 uppercase tracking-tight font-display">ĐĂNG <span class="text-indigo-600">NHẬP</span></h2>
            <p class="text-slate-400 text-xs sm:text-sm font-medium font-sans">Truy cập để tiếp tục mua sắm những mẫu Sneaker đỉnh cao</p>
        </div>

        <?php if($error): ?>
            <div class="bg-rose-50 border border-rose-100 text-rose-600 px-5 py-4 rounded-2xl text-center text-xs font-bold mb-6 font-sans">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block premium-label mb-2.5">Tên đăng nhập</label>
                <input type="text" name="username" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans" placeholder="Nhập tên đăng nhập...">
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-2.5">
                    <label class="block premium-label">Mật khẩu</label>
                    <a href="forgot_password.php" class="text-xs font-bold text-indigo-600 hover:underline premium-btn tracking-wider">Quên mật khẩu?</a>
                </div>
                <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans" placeholder="••••••••">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full btn-gradient text-white text-xs font-bold py-4.5 rounded-2xl shadow-xl uppercase tracking-wider premium-btn">Đăng nhập</button>
            </div>
            
            <p class="text-center text-xs sm:text-sm font-semibold text-slate-400 pt-4 font-sans">
                Chưa có tài khoản? <a href="register.php" class="text-indigo-600 hover:underline decoration-2 underline-offset-4 font-display font-bold uppercase tracking-wider premium-btn text-xs">Đăng ký ngay</a>
            </p>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
