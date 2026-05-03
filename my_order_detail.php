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

    <div class="flex flex-col lg:flex-row gap-12">
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-8">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-xl font-black text-dark uppercase tracking-tight italic">Danh sách sản phẩm</h3>
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest italic"><?= count($items) ?> Món đồ</span>
                </div>
                <div class="divide-y divide-slate-50">
                    <?php foreach($items as $item): ?>
                    <div class="p-8 flex items-center gap-6 group hover:bg-slate-50 transition-colors">
                        <div class="w-24 h-24 bg-slate-50 rounded-2xl overflow-hidden flex-shrink-0 border border-slate-100 p-2">
                            <img src="<?= !empty($item['image_url']) ? 'public' . $item['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=200&q=80' ?>" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-black text-dark text-lg mb-1 leading-tight italic uppercase"><?= htmlspecialchars($item['name']) ?></h4>
                            <p class="text-sm font-bold text-slate-400 italic"><?= format_price($item['price']) ?> x <?= $item['quantity'] ?></p>
                        </div>
                        <div class="text-right">
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
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
