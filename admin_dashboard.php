<?php 
include_once 'config.php';
check_admin();
$title = 'Bảng điều khiển - Admin';
include 'header.php'; 

// Thống kê sơ bộ
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?: 0;
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

// Lấy 5 đơn hàng mới nhất
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
?>

<div class="container mx-auto px-4 py-12">
    <div class="mb-12">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-dark mb-2 uppercase tracking-tight font-display">Admin <span class="text-indigo-600">Dashboard</span></h1>
        <p class="text-slate-400 text-xs sm:text-sm font-medium font-sans">Chào mừng quay trở lại! Dưới đây là tình hình kinh doanh của cửa hàng.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 group hover:bg-indigo-600 transition-all duration-500">
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-indigo-500 transition-colors">
                <svg class="w-6 h-6 text-indigo-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.12em] mb-1 group-hover:text-indigo-200 font-display">Doanh thu (Giao xong)</p>
            <h3 class="text-2xl font-extrabold tracking-tight text-dark group-hover:text-white font-display"><?= format_price($total_revenue) ?></h3>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 group hover:bg-emerald-600 transition-all duration-500">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500 transition-colors">
                <svg class="w-6 h-6 text-emerald-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.12em] mb-1 group-hover:text-emerald-200 font-display">Tổng đơn hàng</p>
            <h3 class="text-2xl font-extrabold tracking-tight text-dark group-hover:text-white font-display"><?= $total_orders ?> đơn</h3>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 group hover:bg-amber-600 transition-all duration-500">
            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-500 transition-colors">
                <svg class="w-6 h-6 text-amber-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.12em] mb-1 group-hover:text-amber-200 font-display">Sản phẩm</p>
            <h3 class="text-2xl font-extrabold tracking-tight text-dark group-hover:text-white font-display"><?= $total_products ?> mẫu</h3>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 group hover:bg-blue-600 transition-all duration-500">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-500 transition-colors">
                <svg class="w-6 h-6 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.12em] mb-1 group-hover:text-blue-200 font-display">Khách hàng</p>
            <h3 class="text-2xl font-extrabold tracking-tight text-dark group-hover:text-white font-display"><?= $total_users ?> thành viên</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Quick Links -->
        <div class="lg:col-span-1 space-y-6">
            <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-6 font-display">Thao tác nhanh</h4>
            <div class="flex flex-col gap-4">
                <a href="admin_products.php" class="flex items-center gap-4 bg-white p-6 rounded-3xl border border-slate-100 hover:border-indigo-600 hover:shadow-xl hover:shadow-indigo-500/10 transition-all group">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </div>
                    <span class="font-bold text-slate-800 font-display text-sm tracking-tight">Quản lý danh sách giày</span>
                </a>
                <a href="admin_orders.php" class="flex items-center gap-4 bg-white p-6 rounded-3xl border border-slate-100 hover:border-indigo-600 hover:shadow-xl hover:shadow-indigo-500/10 transition-all group">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <span class="font-bold text-slate-800 font-display text-sm tracking-tight">Xem danh sách đơn hàng</span>
                </a>
                <a href="admin_return_requests.php" class="flex items-center gap-4 bg-white p-6 rounded-3xl border border-slate-100 hover:border-indigo-600 hover:shadow-xl hover:shadow-indigo-500/10 transition-all group">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                    </div>
                    <span class="font-bold text-slate-800 font-display text-sm tracking-tight">Quản lý đổi trả hàng</span>
                </a>
                <a href="admin_brands.php" class="flex items-center gap-4 bg-white p-6 rounded-3xl border border-slate-100 hover:border-indigo-600 hover:shadow-xl hover:shadow-indigo-500/10 transition-all group">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <span class="font-bold text-slate-800 font-display text-sm tracking-tight">Quản lý Thương hiệu</span>
                </a>
                <a href="admin_categories.php" class="flex items-center gap-4 bg-white p-6 rounded-3xl border border-slate-100 hover:border-indigo-600 hover:shadow-xl hover:shadow-indigo-500/10 transition-all group">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="font-bold text-slate-800 font-display text-sm tracking-tight">Quản lý Danh mục</span>
                </a>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="lg:col-span-2 space-y-6 font-sans">
            <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 font-display">Đơn hàng mới nhất</h4>
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[500px]">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-4 font-bold text-[10px] uppercase tracking-widest text-slate-400 premium-table-header">Mã</th>
                            <th class="px-8 py-4 font-bold text-[10px] uppercase tracking-widest text-slate-400 premium-table-header">Khách hàng</th>
                            <th class="px-8 py-4 font-bold text-[10px] uppercase tracking-widest text-slate-400 premium-table-header">Tiền</th>
                            <th class="px-8 py-4 font-bold text-[10px] uppercase tracking-widest text-slate-400 premium-table-header">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-700 text-sm">
                        <?php foreach($recent_orders as $o): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-5 font-semibold">#<?= $o['id'] ?></td>
                            <td class="px-8 py-5 font-medium"><?= htmlspecialchars($o['shipping_name']) ?></td>
                            <td class="px-8 py-5 font-bold text-indigo-600 premium-price"><?= format_price($o['total_amount']) ?></td>
                            <td class="px-8 py-5">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest bg-amber-100 text-amber-700 font-sans">
                                    <?= $o['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <div class="p-6 bg-slate-50/50 text-center font-display">
                    <a href="admin_orders.php" class="text-xs font-bold text-indigo-600 uppercase tracking-widest hover:underline premium-btn">Xem tất cả đơn hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
