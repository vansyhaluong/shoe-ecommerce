<?php 
include_once 'config.php';
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Kiểm tra đơn hàng có thuộc về user này không
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if(!$order) {
    die("Đơn hàng không tồn tại hoặc bạn không có quyền xem!");
}

// Xử lý gửi yêu cầu đổi trả
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_return') {
    $reason = trim($_POST['reason'] ?? '');
    
    // Bảo mật: Chỉ cho phép đơn hàng có status completed hoặc paid
    if ($order['status'] !== 'completed' && $order['status'] !== 'paid') {
        $error = 'Đơn hàng chưa hoàn thành hoặc chưa thanh toán, không thể yêu cầu đổi trả!';
    } elseif (empty($reason)) {
        $error = 'Vui lòng nhập lý do đổi trả hàng!';
    } else {
        try {
            $stmt_ins = $pdo->prepare("INSERT INTO return_requests (order_id, user_id, reason, status) VALUES (?, ?, ?, 'pending')");
            $stmt_ins->execute([$order_id, $user_id, $reason]);
            $success = 'Gửi yêu cầu đổi trả thành công! Vui lòng chờ quản trị viên phê duyệt.';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000 || $e->getCode() == '23000') { // Lỗi trùng UNIQUE key
                $error = 'Bạn đã gửi yêu cầu đổi trả cho đơn hàng này rồi!';
            } else {
                $error = 'Có lỗi xảy ra: ' . $e->getMessage();
            }
        }
    }
}

// Lấy thông tin yêu cầu đổi trả nếu có
$stmt_ret = $pdo->prepare("SELECT * FROM return_requests WHERE order_id = ?");
$stmt_ret->execute([$order_id]);
$return_request = $stmt_ret->fetch();

$title = 'Chi tiết đơn hàng #' . $order_id;
include 'header.php'; 

// Lấy sản phẩm trong đơn hàng
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, pi.image_url 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<div class="container mx-auto px-4 py-12">
    <a href="my_orders.php" class="inline-flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-indigo-600 mb-8 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Quay lại đơn hàng của tôi
    </a>

    <?php if($error): ?>
        <div class="bg-rose-50 border border-rose-100 text-rose-600 px-6 py-4 rounded-2xl font-bold mb-8 text-sm">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl font-bold mb-8 text-sm">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="flex flex-col lg:flex-row gap-12">
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-8">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-xl font-black text-dark uppercase tracking-tight italic">Danh sách sản phẩm</h3>
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest italic"><?= count($items) ?> Món đồ</span>
                </div>
                <div class="divide-y divide-slate-50">
                    <?php foreach($items as $item): ?>
                    <div class="p-8 flex flex-col sm:flex-row items-center sm:items-center gap-6 group hover:bg-slate-50 transition-colors">
                        <div class="w-24 h-24 bg-slate-50 rounded-2xl overflow-hidden flex-shrink-0 border border-slate-100 p-2">
                            <img src="<?= !empty($item['image_url']) ? 'public' . $item['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=200&q=80' ?>" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-grow w-full text-center sm:text-left">
                            <h4 class="font-black text-dark text-lg mb-1 leading-tight italic uppercase"><?= htmlspecialchars($item['name']) ?></h4>
                            <div class="flex flex-col gap-1 mt-1 items-center sm:items-start">
                                <p class="text-sm font-bold text-slate-400 italic"><?= format_price($item['price']) ?> x <?= $item['quantity'] ?></p>
                                <?php if(!empty($item['size'])): ?>
                                    <span class="text-xs font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2.5 py-0.5 rounded w-fit">Size: <?= $item['size'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-center sm:text-right w-full sm:w-auto border-t sm:border-none pt-4 sm:pt-0">
                            <span class="text-lg font-black text-dark italic"><?= format_price($item['price'] * $item['quantity']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="p-8 bg-indigo-600 flex justify-between items-center text-white">
                    <span class="font-black uppercase tracking-widest italic">Tổng tiền thanh toán</span>
                    <span class="text-3xl font-black italic"><?= format_price($order['total_amount']) ?></span>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 space-y-8">
                <div>
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Thông tin giao hàng</h4>
                    <p class="font-black text-dark text-lg italic uppercase mb-1"><?= htmlspecialchars($order['shipping_name']) ?></p>
                    <p class="text-slate-500 font-medium italic text-sm mb-1"><?= htmlspecialchars($order['shipping_phone']) ?></p>
                    <p class="text-slate-500 font-medium italic text-sm leading-relaxed"><?= htmlspecialchars($order['shipping_address']) ?></p>
                </div>
                
                <div class="pt-8 border-t border-slate-100">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Trạng thái hiện tại</h4>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-amber-400 animate-pulse"></div>
                        <span class="font-black text-dark uppercase tracking-widest text-sm"><?= $order['status'] ?></span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold italic mt-2 uppercase tracking-tighter">Cập nhật lúc: <?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></p>
                </div>

                <?php if ($order['status'] === 'completed' || $order['status'] === 'paid'): ?>
                <div class="pt-8 border-t border-slate-100">
                    <a href="print_invoice.php?id=<?= $order['id'] ?>" target="_blank" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl flex items-center justify-center gap-2 text-xs uppercase tracking-widest transition-all shadow-md shadow-indigo-600/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h8z"></path></svg>
                        In hóa đơn
                    </a>
                </div>
                <?php endif; ?>

                <?php 
                if ($return_request): 
                    $ret_status_colors = [
                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm shadow-amber-500/5',
                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-sm shadow-emerald-500/5',
                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200 shadow-sm shadow-rose-500/5'
                    ];
                    $ret_status_labels = [
                        'pending' => 'Đang chờ duyệt',
                        'approved' => 'Đã đồng ý đổi trả',
                        'rejected' => 'Đã từ chối đổi trả'
                    ];
                    $ret_color = $ret_status_colors[$return_request['status']] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                    $ret_label = $ret_status_labels[$return_request['status']] ?? $return_request['status'];
                ?>
                <div class="pt-8 border-t border-slate-100 space-y-4">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Yêu cầu đổi trả</h4>
                    <div class="p-5 rounded-2xl border <?= $ret_color ?> space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-current animate-pulse"></span>
                            <p class="text-xs font-black uppercase tracking-wider">Trạng thái: <?= $ret_label ?></p>
                        </div>
                        <p class="text-xs font-medium italic"><strong class="font-bold uppercase tracking-tight">Lý do của bạn:</strong> <?= htmlspecialchars($return_request['reason']) ?></p>
                        <?php if ($return_request['admin_note']): ?>
                            <div class="text-xs font-medium border-t border-current/15 pt-3 mt-3 space-y-1">
                                <p class="font-black uppercase tracking-wider text-[10px]">Phản hồi từ Admin:</p>
                                <p class="italic"><?= htmlspecialchars($return_request['admin_note']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php elseif ($order['status'] === 'completed' || $order['status'] === 'paid'): ?>
                <div class="pt-8 border-t border-slate-100 space-y-4">
                    <button onclick="document.getElementById('return-form-container').classList.toggle('hidden')" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-4 rounded-2xl flex items-center justify-center gap-2 text-xs uppercase tracking-widest transition-all shadow-md shadow-slate-900/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                        Yêu cầu đổi trả
                    </button>
                    
                    <div id="return-form-container" class="hidden bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-4 animate-fade-in">
                        <form method="POST">
                            <input type="hidden" name="action" value="request_return">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5">Lý do đổi trả hàng</label>
                                <textarea name="reason" required rows="3" class="w-full bg-white border border-slate-200 focus:border-indigo-500 rounded-xl p-3.5 text-xs font-medium focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-all placeholder:text-slate-300" placeholder="Vui lòng nhập lý do cụ thể..."></textarea>
                            </div>
                            <button type="submit" class="w-full mt-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 rounded-xl text-2xs uppercase tracking-widest transition-all shadow-md shadow-indigo-600/10">
                                Gửi yêu cầu
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
</style>
<?php include 'footer.php'; ?>
