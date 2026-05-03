<?php 
include 'header.php'; 

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT p.*, pi.image_url 
    FROM products p 
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
    WHERE p.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

// Lấy thêm ảnh phụ (bao gồm cả ảnh chính)
$stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY is_primary DESC");
$stmt->execute([$id]);
$images = $stmt->fetchAll();

// Lấy sản phẩm liên quan
$stmt = $pdo->prepare("
    SELECT p.*, pi.image_url 
    FROM products p 
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
    WHERE p.category = ? AND p.id != ? 
    LIMIT 4
");
$stmt->execute([$product['category'], $id]);
$relatedProducts = $stmt->fetchAll();
?>

<!-- Breadcrumbs -->
<div class="bg-white border-b border-slate-100 py-4">
    <div class="container mx-auto px-4 text-xs font-bold uppercase tracking-widest text-slate-400">
        <a href="index.php" class="hover:text-indigo-600 transition">Trang chủ</a>
        <span class="mx-2">/</span>
        <a href="products.php" class="hover:text-indigo-600 transition">Sản phẩm</a>
        <span class="mx-2">/</span>
        <span class="text-indigo-600"><?= htmlspecialchars($product['name']) ?></span>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-16 mb-24">
        <!-- Image Gallery -->
        <div class="w-full lg:w-1/2 space-y-4">
            <div class="relative pb-[100%] bg-white rounded-[3rem] overflow-hidden shadow-sm border border-slate-100 group">
                <img id="main-image" src="<?= !empty($product['image_url']) ? 'public' . $product['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80' ?>" class="absolute inset-0 w-full h-full object-contain p-12 transition-transform duration-700 hover:scale-110">
                <div class="absolute top-8 left-8">
                    <span class="bg-indigo-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg">New Release</span>
                </div>
            </div>
            
            <?php if(count($images) > 1): ?>
            <div class="grid grid-cols-5 gap-4">
                <?php foreach($images as $img): ?>
                <div class="cursor-pointer bg-white rounded-2xl overflow-hidden border-2 border-transparent hover:border-indigo-600 transition-all p-2 aspect-square group" onclick="document.getElementById('main-image').src = 'public<?= $img['image_url'] ?>'">
                    <img src="public<?= $img['image_url'] ?>" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-transform">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center">
            <div class="mb-8">
                <p class="text-sm font-black text-indigo-500 uppercase tracking-[0.2em] mb-2"><?= htmlspecialchars($product['brand']) ?></p>
                <h1 class="text-4xl sm:text-5xl font-black text-dark mb-4 leading-tight"><?= htmlspecialchars($product['name']) ?></h1>
                <div class="flex items-center gap-6 mb-6">
                    <span class="text-4xl font-black text-dark"><?= format_price($product['price']) ?></span>
                    <div class="flex items-center gap-1 bg-amber-50 px-3 py-1 rounded-full">
                        <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span class="text-sm font-black text-amber-700">4.9 (128 reviews)</span>
                    </div>
                </div>
                <p class="text-slate-500 text-lg leading-relaxed"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>

            <form action="cart_action.php?action=add" method="POST" class="space-y-10">
                <input type="hidden" name="product_id" value="<?= $id ?>">
                
                <!-- Color Selector -->
                <div>
                    <h4 class="text-xs font-black text-dark uppercase tracking-widest mb-4">Chọn màu sắc</h4>
                    <div class="flex gap-4">
                        <?php 
                        // Demo logic: gán mỗi màu với một ảnh nếu có nhiều ảnh
                        $colorImages = [];
                        if(!empty($images)) {
                            $colorImages['primary'] = 'public' . ($images[0]['image_url'] ?? $product['image_url']);
                            $colorImages['black'] = isset($images[1]) ? 'public' . $images[1]['image_url'] : $colorImages['primary'];
                            $colorImages['white'] = isset($images[2]) ? 'public' . $images[2]['image_url'] : $colorImages['primary'];
                        }
                        ?>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="color" value="primary" checked class="peer sr-only" 
                                   onclick="document.getElementById('main-image').src='<?= $colorImages['primary'] ?? '' ?>'">
                            <div class="w-10 h-10 rounded-full bg-indigo-600 border-4 border-white shadow-sm ring-2 ring-transparent peer-checked:ring-indigo-600 transition-all"></div>
                        </label>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="color" value="black" class="peer sr-only"
                                   onclick="document.getElementById('main-image').src='<?= $colorImages['black'] ?? '' ?>'">
                            <div class="w-10 h-10 rounded-full bg-slate-900 border-4 border-white shadow-sm ring-2 ring-transparent peer-checked:ring-slate-900 transition-all"></div>
                        </label>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="color" value="white" class="peer sr-only"
                                   onclick="document.getElementById('main-image').src='<?= $colorImages['white'] ?? '' ?>'">
                            <div class="w-10 h-10 rounded-full bg-slate-200 border-4 border-white shadow-sm ring-2 ring-transparent peer-checked:ring-slate-400 transition-all"></div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="w-32">
                        <h4 class="text-xs font-black text-dark uppercase tracking-widest mb-4">Số lượng</h4>
                        <div class="flex items-center bg-slate-100 rounded-2xl p-1">
                            <button type="button" class="w-10 h-10 flex items-center justify-center font-bold text-slate-500" onclick="let q = document.getElementById('qty'); q.value = Math.max(1, parseInt(q.value)-1)">-</button>
                            <input type="number" id="qty" name="quantity" value="1" min="1" class="w-10 bg-transparent text-center font-black text-dark focus:outline-none border-none">
                            <button type="button" class="w-10 h-10 flex items-center justify-center font-bold text-slate-500" onclick="let q = document.getElementById('qty'); q.value = parseInt(q.value)+1">+</button>
                        </div>
                    </div>
                    <div class="flex-grow pt-8">
                        <button type="submit" class="w-full btn-gradient text-white font-black py-5 rounded-[2rem] shadow-xl flex items-center justify-center gap-3 uppercase tracking-widest">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="mb-24">
        <div class="flex flex-wrap border-b border-slate-100 gap-8 mb-12">
            <button onclick="switchTab('desc')" id="tab-btn-desc" class="pb-4 text-sm font-black uppercase tracking-widest text-indigo-600 border-b-4 border-indigo-600 transition-all">Mô tả sản phẩm</button>
            <button onclick="switchTab('specs')" id="tab-btn-specs" class="pb-4 text-sm font-black uppercase tracking-widest text-slate-400 border-b-4 border-transparent hover:text-dark transition-all">Thông số kỹ thuật</button>
            <button onclick="switchTab('reviews')" id="tab-btn-reviews" class="pb-4 text-sm font-black uppercase tracking-widest text-slate-400 border-b-4 border-transparent hover:text-dark transition-all">Đánh giá (128)</button>
        </div>

        <div id="tab-content-desc" class="tab-content animate-[fadeIn_0.5s_ease-out]">
            <div class="max-w-4xl prose prose-slate">
                <h3 class="text-2xl font-black text-dark mb-6">Trải nghiệm sự khác biệt với công nghệ mới</h3>
                <p class="text-slate-600 text-lg leading-relaxed mb-6">Sản phẩm này không chỉ là một đôi giày, nó là một tác phẩm nghệ thuật của công nghệ và thời trang. Với thiết kế tối giản nhưng đầy tinh tế, đôi giày mang lại cảm giác nhẹ nhàng, linh hoạt cho đôi chân trong mọi hoạt động.</p>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 list-none p-0">
                    <li class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-50"><svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> <span class="font-bold">Đế đệm khí êm ái</span></li>
                    <li class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-50"><svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> <span class="font-bold">Chất liệu vải Mesh thoáng khí</span></li>
                    <li class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-50"><svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> <span class="font-bold">Độ bền vượt trội (1000km+)</span></li>
                    <li class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-50"><svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> <span class="font-bold">Dễ dàng vệ sinh</span></li>
                </ul>
            </div>
        </div>

        <div id="tab-content-specs" class="tab-content hidden animate-[fadeIn_0.5s_ease-out]">
            <div class="max-w-2xl bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-50">
                        <tr><th class="px-8 py-4 font-black text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50 w-1/3">Thương hiệu</th><td class="px-8 py-4 font-bold text-dark"><?= htmlspecialchars($product['brand']) ?></td></tr>
                        <tr><th class="px-8 py-4 font-black text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50">Mã sản phẩm</th><td class="px-8 py-4 font-bold text-dark">SS-<?= $product['id'] ?></td></tr>
                        <tr><th class="px-8 py-4 font-black text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50">Danh mục</th><td class="px-8 py-4 font-bold text-dark"><?= htmlspecialchars($product['category']) ?></td></tr>
                        <tr><th class="px-8 py-4 font-black text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50">Năm sản xuất</th><td class="px-8 py-4 font-bold text-dark">2026</td></tr>
                        <tr><th class="px-8 py-4 font-black text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50 text-indigo-600">Bảo hành</th><td class="px-8 py-4 font-black text-indigo-600 uppercase">12 Tháng</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-content-reviews" class="tab-content hidden animate-[fadeIn_0.5s_ease-out]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-indigo-50 p-8 rounded-[2rem] border border-indigo-100 flex items-center justify-center flex-col text-center">
                    <span class="text-6xl font-black text-indigo-600 mb-2">4.9</span>
                    <div class="flex text-amber-400 mb-4">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                    <p class="text-indigo-900 font-bold tracking-widest uppercase text-xs">Điểm số trung bình</p>
                </div>
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 flex gap-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-black">N</div>
                        <div>
                            <h5 class="font-bold text-dark">Nguyễn Văn A <span class="text-xs text-slate-400 font-medium ml-2">2 ngày trước</span></h5>
                            <p class="text-sm text-slate-500 mt-2 italic">"Giày rất đẹp, ôm chân và cực kỳ nhẹ. Giao hàng nhanh hơn mong đợi!"</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 flex gap-4">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-dark flex items-center justify-center font-black">T</div>
                        <div>
                            <h5 class="font-bold text-dark">Trần Thị B <span class="text-xs text-slate-400 font-medium ml-2">1 tuần trước</span></h5>
                            <p class="text-sm text-slate-500 mt-2 italic">"Màu sắc đúng như hình, đi bộ cả ngày không bị đau chân. Sẽ ủng hộ tiếp!"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if(!empty($relatedProducts)): ?>
    <div>
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black text-dark mb-4 uppercase italic">Có Thể Bạn <span class="text-indigo-600">Thích</span></h2>
            <div class="w-20 h-1.5 bg-indigo-600 mx-auto rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <?php foreach($relatedProducts as $related): ?>
            <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all border border-slate-100 group card-hover">
                <div class="relative pb-[110%] bg-slate-50/50 p-6">
                    <a href="product_detail.php?id=<?= $related['id'] ?>" class="absolute inset-0 z-10"></a>
                    <img src="<?= !empty($related['image_url']) ? 'public' . $related['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80' ?>" class="absolute inset-0 w-full h-full object-contain p-8 mix-blend-multiply group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="p-6">
                    <p class="text-xs font-black text-indigo-500 uppercase tracking-widest mb-1"><?= htmlspecialchars($related['brand']) ?></p>
                    <h3 class="text-lg font-black text-dark mb-4 leading-tight">
                        <a href="product_detail.php?id=<?= $related['id'] ?>" class="hover:text-indigo-600 transition-colors line-clamp-2"><?= htmlspecialchars($related['name']) ?></a>
                    </h3>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-black text-dark"><?= format_price($related['price']) ?></span>
                        <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function switchTab(tabId) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    
    // Reset all tabs styles
    const tabs = ['desc', 'specs', 'reviews'];
    tabs.forEach(t => {
        let el = document.getElementById('tab-btn-' + t);
        el.classList.remove('text-indigo-600', 'border-indigo-600', 'font-black');
        el.classList.add('text-slate-400', 'border-transparent', 'hover:text-dark');
    });
    
    // Show active content
    document.getElementById('tab-content-' + tabId).classList.remove('hidden');
    
    // Set active tab style
    let activeTab = document.getElementById('tab-btn-' + tabId);
    activeTab.classList.remove('text-slate-400', 'border-transparent', 'hover:text-dark');
    activeTab.classList.add('text-indigo-600', 'border-indigo-600', 'font-black');
}
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php include 'footer.php'; ?>
