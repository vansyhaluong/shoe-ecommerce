<?php 
$title = 'Quên mật khẩu - ShoeStore';
include 'header.php'; 

$error = '';
$success = '';
$step = 1;
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'verify') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND email = ?");
        $stmt->execute([$username, $email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['reset_user_id'] = $user['id'];
            $step = 2;
        } else {
            $error = 'Tên đăng nhập hoặc email không chính xác!';
        }
    } elseif ($action === 'reset') {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (strlen($new_password) < 6) {
            $error = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
            $step = 2;
        } elseif ($new_password !== $confirm_password) {
            $error = 'Mật khẩu xác nhận không khớp!';
            $step = 2;
        } elseif (!isset($_SESSION['reset_user_id'])) {
            $error = 'Yêu cầu không hợp lệ. Vui lòng thực hiện lại từ bước 1!';
            $step = 1;
        } else {
            $user_id = $_SESSION['reset_user_id'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);
            
            unset($_SESSION['reset_user_id']);
            $success = 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay bây giờ.';
            $step = 3;
        }
    }
}
?>

<div class="container mx-auto px-4 py-16 flex items-center justify-center min-h-[calc(100vh-200px)]">
    <div class="w-full max-w-lg bg-white p-10 md:p-12 rounded-[2.5rem] shadow-2xl border border-slate-100 hover:shadow-indigo-500/5 transition-all duration-500">
        <div class="text-center mb-10">
            <span class="bg-indigo-50 text-indigo-600 text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest mb-4 inline-block">Mật khẩu mới</span>
            <h2 class="text-4xl font-black text-dark mb-3 uppercase italic tracking-tight">QUÊN <span class="text-indigo-600">MẬT KHẨU</span></h2>
            <p class="text-slate-400 text-sm font-medium">Lấy lại quyền truy cập vào tài khoản mua sắm của bạn</p>
        </div>

        <?php if($error): ?>
            <div class="bg-rose-50 border border-rose-100 text-rose-600 px-5 py-4 rounded-2xl text-center text-sm font-bold mb-6">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-5 py-4 rounded-2xl text-center text-sm font-bold mb-6">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- STEP 1: Xác nhận tên tài khoản & Email -->
        <?php if($step === 1): ?>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="verify">
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2.5">Tên đăng nhập</label>
                <input type="text" name="username" required value="<?= htmlspecialchars($username) ?>" class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" placeholder="Nhập tên đăng nhập của bạn...">
            </div>
            
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2.5">Địa chỉ Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>" class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" placeholder="example@domain.com">
            </div>

            <div class="pt-2 flex flex-col gap-4">
                <button type="submit" class="w-full btn-gradient text-white font-black py-4.5 rounded-[2rem] shadow-xl uppercase tracking-widest text-sm">Xác nhận thông tin</button>
                <a href="login.php" class="w-full border border-slate-200 text-slate-500 hover:bg-slate-50 text-center font-black py-4 rounded-[2rem] uppercase tracking-widest text-xs transition-all">Quay lại Đăng nhập</a>
            </div>
        </form>
        <?php endif; ?>

        <!-- STEP 2: Tạo mật khẩu mới -->
        <?php if($step === 2): ?>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="reset">
            <div class="bg-indigo-50 text-indigo-700 px-5 py-4 rounded-2xl text-center text-xs font-bold mb-4">
                Xác thực thành công! Vui lòng đặt lại mật khẩu mới cho tài khoản của bạn.
            </div>

            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2.5">Mật khẩu mới</label>
                <input type="password" name="new_password" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" placeholder="•••••••• (Tối thiểu 6 ký tự)">
            </div>
            
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2.5">Xác nhận mật khẩu mới</label>
                <input type="password" name="confirm_password" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" placeholder="••••••••">
            </div>

            <div class="pt-2 flex flex-col gap-4">
                <button type="submit" class="w-full btn-gradient text-white font-black py-4.5 rounded-[2rem] shadow-xl uppercase tracking-widest text-sm">Đặt lại mật khẩu</button>
                <a href="forgot_password.php" class="w-full border border-slate-200 text-slate-500 hover:bg-slate-50 text-center font-black py-4 rounded-[2rem] uppercase tracking-widest text-xs transition-all">Quay lại bước 1</a>
            </div>
        </form>
        <?php endif; ?>

        <!-- STEP 3: Hoàn thành đặt lại mật khẩu -->
        <?php if($step === 3): ?>
        <div class="text-center space-y-6 pt-4">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="text-slate-500 font-bold italic text-sm">Bây giờ bạn đã có thể truy cập hệ thống bằng mật khẩu mới của mình.</p>
            <a href="login.php" class="w-full btn-gradient text-white font-black py-4.5 rounded-[2rem] shadow-xl uppercase tracking-widest text-sm block">Đăng nhập ngay</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
