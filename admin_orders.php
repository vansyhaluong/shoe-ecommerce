<?php 
include 'config.php';
check_admin();
$title = 'Quản lý đơn hàng - Admin';
include 'header.php'; 

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();
?>

<div class="container mx-auto px-4 py-12">
    <div class="mb-12 text-center md:text-left">
        <h1 class="text-4xl font-black text-dark mb-2 tracking-tight">Quản Lý <span class="text-indigo-600">Đơn Hàng</span></h1>
        <p class="text-slate-500 font-medium italic">Theo dõi và cập nhật trạng thái đơn hàng của khách hàng.</p>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Mã ĐH</th>
                    <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Khách Hàng</th>
                    <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Tổng Tiền</th>
                    <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Trạng Thái</th>
                    <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Ngày Đặt</th>
                    <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400 text-right">Chi Tiết</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($orders as $o): ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-6 font-bold text-dark">#<?= $o['id'] ?></td>
                    <td class="px-8 py-6">
                        <p class="font-bold text-dark"><?= htmlspecialchars($o['shipping_name']) ?></p>
                        <p class="text-xs text-slate-400"><?= htmlspecialchars($o['shipping_phone']) ?></p>
                    </td>
                    <td class="px-8 py-6 font-black text-indigo-600"><?= format_price($o['total_amount']) ?></td>
                    <td class="px-8 py-6">
                        <?php 
                        $status_colors = [
                            'pending' => 'bg-amber-100 text-amber-700',
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'cancelled' => 'bg-red-100 text-red-700'
                        ];
                        $color = $status_colors[$o['status']] ?? 'bg-slate-100 text-slate-700';
                        ?>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?= $color ?>">
                            <?= $o['status'] ?>
                        </span>
                    </td>
                    <td class="px-8 py-6 text-sm text-slate-500"><?= date('H:i d/m/Y', strtotime($o['created_at'])) ?></td>
                    <td class="px-8 py-6 text-right">
                        <a href="admin_order_detail.php?id=<?= $o['id'] ?>" class="inline-flex items-center gap-2 text-indigo-600 font-bold hover:gap-3 transition-all text-sm">XEM <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
