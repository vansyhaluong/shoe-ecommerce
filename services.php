<?php 
$title = 'Dịch vụ Sneaker - ShoeStore';
include 'header.php'; 

// Kiểm tra danh mục dây giày có tồn tại trong database không
$shoelaces_link = "products.php?q=dây";
try {
    $stmt_cat = $pdo->prepare("SELECT id FROM categories WHERE name LIKE ? OR name LIKE ?");
    $stmt_cat->execute(['%dây%', '%shoelace%']);
    $cat_res = $stmt_cat->fetch();
    if ($cat_res) {
        $shoelaces_link = "products.php?category_id=" . $cat_res['id'];
    }
} catch (PDOException $e) {
    // Fallback if table doesn't support category query
}
?>

<div class="bg-slate-50 min-h-[calc(100vh-250px)] py-16">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Section Header -->
        <div class="text-center mb-20">
            <span class="inline-block px-4 py-1 bg-black text-white text-[10px] font-black tracking-widest uppercase mb-4 rounded-full">ShoeStore Care</span>
            <h1 class="text-4xl sm:text-6xl font-black text-slate-900 leading-none uppercase tracking-tighter mb-4">DỊCH VỤ <span class="text-slate-500 font-light italic">SNEAKER</span></h1>
            <div class="w-16 h-1 bg-black mx-auto"></div>
        </div>

        <!-- 2 Large Service Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Card 1: Vệ sinh giày -->
            <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-slate-200/50 border border-slate-200/60 transition-all duration-500 group flex flex-col justify-between h-[600px] relative">
                <div class="absolute inset-0 bg-gradient-to-b from-black/20 to-black/80 z-10"></div>
                <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Vệ sinh giày" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                
                <div class="relative z-20 p-10 flex flex-col justify-between h-full text-white">
                    <span class="text-xs font-black uppercase tracking-[0.25em] text-slate-300">Dịch vụ Spa & Bảo dưỡng</span>
                    
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-black leading-tight mb-4 uppercase">VỆ SINH GIÀY</h2>
                        <p class="text-slate-200 text-sm font-medium leading-relaxed max-w-sm mb-6">Dịch vụ vệ sinh giày chuyên nghiệp, giúp đôi giày của bạn luôn sạch đẹp và bền hơn.</p>
                        
                        <button onclick="openCleaningModal()" class="inline-flex items-center gap-3 bg-white text-black font-black px-8 py-4 rounded-full hover:bg-slate-100 transition-all text-xs tracking-widest uppercase shadow-lg select-none">
                            Xem dịch vụ
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 2: Dây giày -->
            <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-slate-200/50 border border-slate-200/60 transition-all duration-500 group flex flex-col justify-between h-[600px] relative">
                <div class="absolute inset-0 bg-gradient-to-b from-black/20 to-black/80 z-10"></div>
                <img src="https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Dây giày" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                
                <div class="relative z-20 p-10 flex flex-col justify-between h-full text-white">
                    <span class="text-xs font-black uppercase tracking-[0.25em] text-slate-300">Phụ kiện Sneakers</span>
                    
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-black leading-tight mb-4 uppercase">DÂY GIÀY SNEAKER</h2>
                        <p class="text-slate-200 text-sm font-medium leading-relaxed max-w-sm mb-6">Nhiều mẫu dây giày chất lượng cao, giúp bạn thay đổi phong cách cho đôi sneaker.</p>
                        
                        <a href="products.php?category=shoelaces" class="inline-flex items-center gap-3 bg-white text-black font-black px-8 py-4 rounded-full hover:bg-slate-100 transition-all text-xs tracking-widest uppercase shadow-lg select-none">
                            Xem sản phẩm
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Premium Modal Drawer for Shoe Cleaning Service -->
<div id="cleaning-modal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4">
    <!-- Backdrop overlay -->
    <div onclick="closeCleaningModal()" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity duration-500 opacity-0" id="modal-backdrop"></div>
    
    <!-- Modal container -->
    <div class="relative bg-white rounded-[2.5rem] shadow-[0_30px_70px_rgba(0,0,0,0.3)] max-w-2xl w-full max-h-[90vh] overflow-y-auto z-10 border border-slate-100 transform translate-y-8 opacity-0 scale-95 transition-all duration-500 ease-out" id="modal-content">
        <!-- Close button -->
        <button onclick="closeCleaningModal()" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors z-30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="p-8 sm:p-10">
            <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black tracking-widest uppercase rounded-full mb-4">Dịch vụ chi tiết</span>
            <h3 class="text-3xl font-black text-slate-900 uppercase tracking-tight mb-4">Vệ sinh giày chuyên nghiệp</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">Đôi sneaker của bạn xứng đáng nhận được sự chăm sóc tốt nhất. Chúng tôi sử dụng các giải pháp làm sạch sinh học cao cấp kết hợp công nghệ sấy khô khử mùi tiên tiến để phục hồi độ tươi mới cho đôi giày.</p>
            
            <!-- Features bullet list -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        🫧
                    </div>
                    <span class="text-xs font-bold text-slate-700">Vệ sinh bằng tay chuyên sâu</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        ⚡
                    </div>
                    <span class="text-xs font-bold text-slate-700">Khử trùng bằng tia UV</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        🌬️
                    </div>
                    <span class="text-xs font-bold text-slate-700">Khử mùi thảo mộc</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        🛡️
                    </div>
                    <span class="text-xs font-bold text-slate-700">Sấy lạnh bảo vệ keo giày</span>
                </div>
            </div>

            <!-- Price placeholder packages -->
            <div class="mb-8">
                <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Bảng giá tham khảo</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-white border border-slate-200 rounded-2xl">
                        <div>
                            <p class="text-sm font-black text-slate-900">Gói Tiêu Chuẩn (Standard Care)</p>
                            <p class="text-xs text-slate-400 font-medium">Bao gồm làm sạch bề mặt, đế giày và khử mùi</p>
                        </div>
                        <span class="text-sm font-black text-indigo-600">120.000đ</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-white border border-slate-200 rounded-2xl">
                        <div>
                            <p class="text-sm font-black text-slate-900">Gói Chăm Sóc Sâu (Deep Clean)</p>
                            <p class="text-xs text-slate-400 font-medium">Làm sạch sâu chi tiết dây, lót, khử ố vàng và dưỡng chất liệu</p>
                        </div>
                        <span class="text-sm font-black text-indigo-600">180.000đ</span>
                    </div>
                </div>
            </div>

            <!-- Quick Booking form inside modal -->
            <div>
                <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Đặt lịch nhanh</h4>
                <form onsubmit="handleBooking(event)" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="text" id="booking-name" required placeholder="Họ và tên..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <input type="tel" id="booking-phone" required placeholder="Số điện thoại..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <button type="submit" class="w-full bg-black text-white hover:bg-slate-900 font-black py-4 rounded-xl text-xs uppercase tracking-widest transition-all">Gửi yêu cầu đặt lịch</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openCleaningModal() {
    const modal = document.getElementById('cleaning-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const content = document.getElementById('modal-content');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        
        content.classList.remove('translate-y-8', 'opacity-0', 'scale-95');
        content.classList.add('translate-y-0', 'opacity-100', 'scale-100');
    }, 50);
}

function closeCleaningModal() {
    const modal = document.getElementById('cleaning-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const content = document.getElementById('modal-content');
    
    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    
    content.classList.remove('translate-y-0', 'opacity-100', 'scale-100');
    content.classList.add('translate-y-8', 'opacity-0', 'scale-95');
    
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 400);
}

function handleBooking(event) {
    event.preventDefault();
    const name = document.getElementById('booking-name').value;
    const phone = document.getElementById('booking-phone').value;
    
    closeCleaningModal();
    
    // Reset form
    document.getElementById('booking-name').value = '';
    document.getElementById('booking-phone').value = '';
    
    // Trigger success toast using global showToast helper from header
    if (typeof showToast === 'function') {
        showToast(`Cảm ơn ${name}! Chúng tôi đã nhận được lịch đặt vệ sinh giày của bạn.`, 'success');
    } else {
        alert('Đặt lịch thành công! Chúng tôi sẽ liên hệ với bạn sớm.');
    }
}
</script>

<?php include 'footer.php'; ?>
