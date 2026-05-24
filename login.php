<?php 
$title = 'Đăng nhập - ShoeStore';
include 'header.php'; 

$error = '';
$old_username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_text($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $old_username = $username;

    if (!validate_string_len($username, 3, 50)) {
        $error = 'Tên đăng nhập phải từ 3 đến 50 ký tự!';
    } elseif (empty($password)) {
        $error = 'Mật khẩu không được để trống!';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                $url = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                redirect($url);
            } else {
                $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
            }
        } catch (PDOException $e) {
            $error = 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau!';
        }
    }
}
?>

<div class="container mx-auto px-4 py-20 flex items-center justify-center min-h-[calc(100vh-200px)]">
    <div class="w-full max-w-lg bg-white p-10 md:p-14 rounded-[2.5rem] shadow-[0_20px_50px_-12px_rgba(15,23,42,0.06)] border border-slate-100/80 hover:shadow-[0_25px_60px_-10px_rgba(99,102,241,0.08)] transition-all duration-500">
        <div class="text-center mb-10">
            <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-4 py-1.5 rounded-full uppercase tracking-[0.15em] mb-4 inline-block font-display">Welcome Back</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-dark mb-3 uppercase tracking-tight font-display">ĐĂNG <span class="text-indigo-600">NHẬP</span></h2>
            <p class="text-slate-400 text-xs sm:text-sm font-medium font-sans">Truy cập để tiếp tục mua sắm những mẫu Sneaker đẳng cấp</p>
        </div>

        <?php if($error): ?>
            <div class="flex items-center gap-3 bg-rose-50/60 border border-rose-100/80 text-rose-600 px-5 py-4 rounded-2xl text-left text-xs font-bold mb-8 font-sans">
                <svg class="w-5 h-5 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-7">
            <div>
                <label class="block premium-label mb-2.5">Tên đăng nhập</label>
                <input type="text" name="username" value="<?= htmlspecialchars($old_username) ?>" required class="w-full bg-slate-50/50 border border-slate-200 focus:border-slate-900 focus:ring-4 focus:ring-slate-100/60 rounded-2xl px-5 py-5 text-sm font-medium focus:outline-none transition-all font-sans placeholder-slate-400/70" placeholder="Nhập tên đăng nhập...">
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-2.5">
                    <label class="block premium-label">Mật khẩu</label>
                    <a href="forgot_password.php" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors premium-btn tracking-wider">Quên mật khẩu?</a>
                </div>
                <input type="password" name="password" required class="w-full bg-slate-50/50 border border-slate-200 focus:border-slate-900 focus:ring-4 focus:ring-slate-100/60 rounded-2xl px-5 py-5 text-sm font-medium focus:outline-none transition-all font-sans placeholder-slate-400/70" placeholder="••••••••">
            </div>

            <div class="pt-4">
                <button type="submit" id="submit-btn" class="w-full relative flex items-center justify-center gap-2.5 bg-slate-950 hover:bg-slate-900 text-white text-xs font-bold py-5 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-300 uppercase tracking-[0.2em] premium-btn select-none group">
                    <span class="btn-text flex items-center gap-2">
                        Đăng nhập
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                    <span class="btn-spinner hidden">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
            
            <p class="text-center text-xs sm:text-sm font-semibold text-slate-400 pt-4 font-sans">
                Chưa có tài khoản? <a href="register.php" class="text-indigo-600 hover:text-indigo-700 transition-colors font-display font-bold uppercase tracking-wider premium-btn text-xs ml-1">Đăng ký ngay</a>
            </p>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            if (btn) {
                const text = btn.querySelector('.btn-text');
                const spinner = btn.querySelector('.btn-spinner');
                if (text && spinner) {
                    text.classList.add('hidden');
                    spinner.classList.remove('hidden');
                    btn.style.pointerEvents = 'none';
                }
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>
