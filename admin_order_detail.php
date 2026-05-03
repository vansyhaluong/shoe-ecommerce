<?php 
include 'config.php';
check_admin();
$title = 'Chi tiết đơn hàng - Admin';
include 'header.php'; 

$order_id = (int)($_GET['id'] ?? 0);

// Lấy thông tin đơn hàng
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) redirect('admin_orders.php');

// Lấy danh sách sản phẩm trong đơn
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, pi.image_url 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    redirect('admin_order_detail.php?id=' . $order_id);
}
?>

<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-12">
        <!-- Thông tin khách hàng & Trạng thái -->
        <div class="w-full lg:w-1/3 space-y-8">
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                <h3 class="text-xl font-black text-dark mb-6 tracking-tight italic uppercase">Thông Tin Đơn Hàng #<?= $order_id ?></h3>
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Người nhận</span>
                        <span class="font-bold text-dark"><?= htmlspecialchars($order['shipping_name']) ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Số điện thoại</span>
                        <span class="font-bold text-dark"><?= htmlspecialchars($order['shipping_phone']) ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Địa chỉ</span>
                        <span class="text-sm font-medium text-slate-600"><?= htmlspecialchars($order['shipping_address']) ?></span>
                    </div>
                </div>

                <hr class="my-8 border-slate-100">

                <form method="POST" class="space-y-4">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest block">Cập nhật trạng thái</label>
                    <select name="status" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500">
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý (Pending)</option>
                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Đang giao hàng</option>
                        <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                    <button type="submit" class="w-full btn-gradient text-white font-bold py-3 rounded-xl shadow-lg">CẬP NHẬT</button>
                </form>

                <hr class="my-8 border-slate-100">
                
                <a href="print_invoice.php?id=<?= $order_id ?>" target="_blank" class="w-full inline-flex items-center justify-center gap-2 bg-slate-900 text-white font-bold py-4 rounded-xl hover:bg-indigo-600 transition-all shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    LẬP & IN HÓA ĐƠN
                </a>
            </div>
            
            <a href="admin_orders.php" class="inline-flex items-center gap-2 text-slate-500 font-bold hover:text-dark transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                QUAY LẠI DANH SÁCH
            </a>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-xl font-black text-dark tracking-tight italic uppercase">Sản phẩm đã đặt</h3>
                    <span class="text-2xl font-black text-indigo-600 italic"><?= format_price($order['total_amount']) ?></span>
                </div>
                <div class="divide-y divide-slate-50">
                    <?php foreach($items as $item): ?>
                    <div class="p-8 flex items-center gap-6 group hover:bg-slate-50 transition-colors">
                        <div class="w-24 h-24 bg-slate-50 rounded-2xl overflow-hidden flex-shrink-0 border border-slate-100 p-2">
                            <img src="<?= !empty($item['image_url']) ? 'public' . $item['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=200&q=80' ?>" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-black text-dark text-lg mb-1 leading-tight"><?= htmlspecialchars($item['name']) ?></h4>
                            <p class="text-sm font-bold text-slate-400 italic"><?= format_price($item['price']) ?> x <?= $item['quantity'] ?></p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-dark italic"><?= format_price($item['price'] * $item['quantity']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
