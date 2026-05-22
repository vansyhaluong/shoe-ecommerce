<?php 
include_once 'config.php';
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$title = 'Đơn hàng của tôi';
include 'header.php'; 

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<div class="container mx-auto px-4 py-12">
    <div class="mb-12">
        <h1 class="text-4xl font-black text-dark mb-2 italic uppercase">Đơn Hàng <span class="text-indigo-600">Của Tôi</span></h1>
        <p class="text-slate-500 font-medium italic text-lg">Theo dõi hành trình của những đôi giày bạn đã chọn.</p>
    </div>

    <?php if(empty($orders)): ?>
    <div class="bg-white rounded-[3rem] p-20 text-center border border-slate-100 shadow-sm">
        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        </div>
        <h3 class="text-2xl font-black text-dark mb-4 uppercase">Bạn chưa có đơn hàng nào</h3>
        <a href="products.php" class="btn-gradient text-white font-bold px-10 py-4 rounded-full inline-block">MUA SẮM NGAY</a>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[700px]">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 font-black text-[10px] uppercase tracking-widest text-slate-400">Mã Đơn</th>
                    <th class="px-8 py-5 font-black text-[10px] uppercase tracking-widest text-slate-400">Ngày Đặt</th>
                    <th class="px-8 py-5 font-black text-[10px] uppercase tracking-widest text-slate-400">Tổng Tiền</th>
                    <th class="px-8 py-5 font-black text-[10px] uppercase tracking-widest text-slate-400">Trạng Thái</th>
                    <th class="px-8 py-5 font-black text-[10px] uppercase tracking-widest text-slate-400"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($orders as $order): ?>
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-6 font-black text-dark text-lg italic">#<?= $order['id'] ?></td>
                    <td class="px-8 py-6 font-bold text-slate-500 text-sm italic"><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                    <td class="px-8 py-6 font-black text-indigo-600 text-xl italic"><?= format_price($order['total_amount']) ?></td>
                    <td class="px-8 py-6">
                        <?php 
                        $statusClass = 'bg-amber-100 text-amber-700';
                        if($order['status'] == 'completed') $statusClass = 'bg-emerald-100 text-emerald-700';
                        if($order['status'] == 'cancelled') $statusClass = 'bg-red-100 text-red-700';
                        ?>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?= $statusClass ?>">
                            <?= $order['status'] ?>
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-6">
                            <?php if ($order['status'] == 'completed' || $order['status'] == 'paid'): ?>
                                <a href="print_invoice.php?id=<?= $order['id'] ?>" target="_blank" class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b-2 border-indigo-600 pb-1 hover:text-indigo-800 hover:border-indigo-800 transition-all">In hóa đơn</a>
                            <?php endif; ?>
                            <a href="my_order_detail.php?id=<?= $order['id'] ?>" class="text-xs font-black text-dark uppercase tracking-widest border-b-2 border-slate-900 pb-1 hover:text-indigo-600 hover:border-indigo-600 transition-all">Chi tiết</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
