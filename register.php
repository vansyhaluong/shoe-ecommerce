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

<div class="container mx-auto px-4 py-16 flex items-center justify-center min-h-[calc(100vh-200px)]">
    <div class="w-full max-w-2xl bg-white p-10 md:p-12 rounded-[2.5rem] shadow-2xl border border-slate-100 hover:shadow-indigo-500/5 transition-all duration-500">
        <div class="text-center mb-10">
            <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-4 py-1.5 rounded-full uppercase tracking-[0.15em] mb-4 inline-block font-display">Create Account</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-dark mb-3 uppercase tracking-tight font-display">ĐĂNG <span class="text-indigo-600">KÝ</span></h2>
            <p class="text-slate-400 text-xs sm:text-sm font-medium font-sans">Đăng ký thành viên để nhận ngay nhiều ưu đãi đặc quyền</p>
        </div>

        <?php if($error): ?>
            <div class="bg-rose-50 border border-rose-100 text-rose-600 px-5 py-4 rounded-2xl text-center text-xs font-bold mb-6 font-sans">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block premium-label mb-2.5">Họ và tên</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans" placeholder="VD: Nguyễn Văn A">
                </div>
                
                <div>
                    <label class="block premium-label mb-2.5">Tên đăng nhập</label>
                    <input type="text" name="username" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans" placeholder="VD: van_a_99">
                </div>
            </div>

            <div>
                <label class="block premium-label mb-2.5">Email</label>
                <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans" placeholder="email@vi-du.com">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block premium-label mb-2.5">Mật khẩu</label>
                    <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans" placeholder="••••••••">
                </div>
                
                <div>
                    <label class="block premium-label mb-2.5">Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans" placeholder="••••••••">
                </div>
            </div>

            <div class="pt-4 text-center">
                <button type="submit" class="w-full btn-gradient text-white text-xs font-bold py-4.5 rounded-2xl shadow-xl uppercase tracking-wider premium-btn">ĐĂNG KÝ NGAY</button>
            </div>
            
            <p class="text-center text-xs sm:text-sm font-semibold text-slate-400 pt-4 font-sans">
                Đã có tài khoản? <a href="login.php" class="text-indigo-600 hover:underline decoration-2 underline-offset-4 font-display font-bold uppercase tracking-wider premium-btn text-xs">Đăng nhập</a>
            </p>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
