<?php
include_once 'config.php';
check_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $request_id = (int)($_POST['request_id'] ?? 0);
    $admin_note = trim($_POST['admin_note'] ?? '');
    
    if ($request_id > 0 && ($action === 'approve' || $action === 'reject')) {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        try {
            $stmt_up = $pdo->prepare("UPDATE return_requests SET status = ?, admin_note = ? WHERE id = ?");
            $stmt_up->execute([$status, $admin_note, $request_id]);
            
            // Tùy chỉnh: Cập nhật trạng thái đơn hàng tương ứng nếu duyệt đổi trả thành công (ví dụ sang 'cancelled' hoặc trạng thái thích hợp, hoặc giữ nguyên để lưu vết)
            $success = 'Cập nhật trạng thái yêu cầu đổi trả thành công!';
        } catch (PDOException $e) {
            $error = 'Lỗi cập nhật: ' . $e->getMessage();
        }
    }
}

// Lấy danh sách tất cả các yêu cầu đổi trả
$stmt = $pdo->query("
    SELECT rr.*, o.shipping_name, o.shipping_phone, o.total_amount, o.status AS order_status, u.username
    FROM return_requests rr
    JOIN orders o ON rr.order_id = o.id
    JOIN users u ON rr.user_id = u.id
    ORDER BY rr.created_at DESC
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = 'Quản lý đổi trả hàng - Admin';
include 'header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="mb-12 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="text-center md:text-left">
            <h1 class="text-4xl font-black text-dark mb-2 tracking-tight">QUẢN LÝ <span class="text-indigo-600">ĐỔI TRẢ HÀNG</span></h1>
            <p class="text-slate-500 font-medium italic">Xem và phê duyệt các yêu cầu đổi hàng hoặc hoàn trả tiền từ khách hàng.</p>
        </div>
        <a href="admin_dashboard.php" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black px-6 py-3.5 rounded-2xl text-xs uppercase tracking-widest transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại Dashboard
        </a>
    </div>

    <?php if($error): ?>
        <div class="bg-rose-50 border border-rose-100 text-rose-600 px-6 py-4 rounded-2xl font-bold mb-8 text-sm animate-fade-in">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl font-bold mb-8 text-sm animate-fade-in">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[950px]">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Đơn Hàng</th>
                        <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Khách Hàng</th>
                        <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Lý Do Đổi Trả</th>
                        <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Trạng Thái</th>
                        <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400">Ngày Gửi</th>
                        <th class="px-8 py-5 font-black text-xs uppercase tracking-widest text-slate-400 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="6" class="px-8 py-16 text-center text-slate-400 font-medium italic">Không tìm thấy yêu cầu đổi trả nào từ khách hàng.</td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach($requests as $r): ?>
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="px-8 py-6">
                            <a href="admin_order_detail.php?id=<?= $r['order_id'] ?>" class="font-black text-indigo-600 hover:text-indigo-700 transition-colors italic text-sm">#<?= $r['order_id'] ?></a>
                            <p class="text-xs font-black text-slate-700 mt-1"><?= format_price($r['total_amount']) ?></p>
                            <span class="inline-block mt-2 px-2 py-0.5 bg-slate-100 text-[9px] font-black uppercase text-slate-500 rounded tracking-wider border border-slate-200"><?= $r['order_status'] ?></span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-black text-dark text-sm uppercase italic"><?= htmlspecialchars($r['shipping_name']) ?></p>
                            <p class="text-xs text-slate-400 mt-0.5">Tài khoản: @<?= htmlspecialchars($r['username']) ?></p>
                            <p class="text-2xs text-slate-400 font-bold"><?= htmlspecialchars($r['shipping_phone']) ?></p>
                        </td>
                        <td class="px-8 py-6 text-xs font-medium text-slate-600 max-w-[280px] break-words italic">
                            "<?= htmlspecialchars($r['reason']) ?>"
                        </td>
                        <td class="px-8 py-6">
                            <?php 
                            $status_colors = [
                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'rejected' => 'bg-rose-50 text-rose-700 border-rose-200'
                            ];
                            $status_labels = [
                                'pending' => 'Đang chờ duyệt',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Đã từ chối'
                            ];
                            $color = $status_colors[$r['status']] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                            $label = $status_labels[$r['status']] ?? $r['status'];
                            ?>
                            <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider border <?= $color ?>">
                                <?= $label ?>
                            </span>
                            
                            <?php if ($r['admin_note']): ?>
                            <div class="text-[10px] text-slate-400 font-semibold italic mt-2.5 max-w-[200px] leading-relaxed border-t border-slate-100 pt-1">
                                <strong class="font-black uppercase tracking-tight text-slate-500 block not-italic">Lưu ý của Admin:</strong>
                                <?= htmlspecialchars($r['admin_note']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-6 text-xs font-bold text-slate-400 uppercase tracking-tighter">
                            <?= date('H:i', strtotime($r['created_at'])) ?>
                            <span class="block text-2xs font-semibold text-slate-400 mt-0.5"><?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
                        </td>
                        <td class="px-8 py-6 text-right vertical-align-middle">
                            <?php if ($r['status'] === 'pending'): ?>
                            <div class="inline-flex gap-3">
                                <button onclick="toggleActionForm(<?= $r['id'] ?>, 'approve')" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-md shadow-emerald-500/20 inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Duyệt
                                </button>
                                <button onclick="toggleActionForm(<?= $r['id'] ?>, 'reject')" class="px-4 py-2.5 bg-slate-950 hover:bg-slate-800 text-white rounded-xl text-[11px] font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-md shadow-slate-950/20 inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Từ Chối
                                </button>
                            </div>
                            
                            <!-- Form nhập phản hồi (ẩn mặc định) -->
                            <div id="form-container-<?= $r['id'] ?>" class="hidden text-left bg-slate-50 p-5 rounded-2xl border border-slate-200 mt-3 max-w-[290px] ml-auto animate-fade-in space-y-4">
                                <form method="POST" class="space-y-4">
                                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="action" id="action-input-<?= $r['id'] ?>" value="">
                                    
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" id="label-note-<?= $r['id'] ?>">Ghi chú phản hồi</label>
                                        <textarea name="admin_note" rows="2" class="w-full bg-white border border-slate-200 focus:border-indigo-500 rounded-xl p-3 text-xs font-medium focus:ring-1 focus:ring-indigo-500 focus:outline-none transition-all placeholder:text-slate-350" placeholder="Nhập phản hồi gửi cho khách..."></textarea>
                                    </div>
                                    <div class="flex gap-2.5 justify-end">
                                        <button type="button" onclick="toggleActionForm(<?= $r['id'] ?>)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Hủy</button>
                                        <button type="submit" class="px-4.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md shadow-indigo-600/15">Xác Nhận</button>
                                    </div>
                                </form>
                            </div>
                            <?php else: ?>
                            <span class="text-[10px] font-black text-slate-400 italic uppercase tracking-wider bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl select-none">Đã xử lý</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleActionForm(id, type = '') {
    const container = document.getElementById('form-container-' + id);
    const actionInput = document.getElementById('action-input-' + id);
    const noteLabel = document.getElementById('label-note-' + id);
    
    if (type) {
        actionInput.value = type;
        noteLabel.textContent = type === 'approve' ? 'Ghi chú duyệt đơn (tùy chọn)' : 'Lý do từ chối (tùy chọn)';
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>

<?php include 'footer.php'; ?>
