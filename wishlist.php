<?php 
$title = 'Danh sách yêu thích - ShoeStore';
include 'header.php'; 

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'wishlist.php';
    redirect('login.php');
}

$user_id = (int)$_SESSION['user_id'];

// Lấy danh sách sản phẩm yêu thích
$stmt = $pdo->prepare("
    SELECT p.*, pi.image_url, b.name AS brand_name 
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    LEFT JOIN brands b ON p.brand_id = b.id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$user_id]);
$wishlistItems = $stmt->fetchAll();
?>

<div class="container mx-auto px-4 py-16 min-h-[calc(100vh-250px)]">
    <!-- Header -->
    <div class="text-center mb-16">
        <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-4 py-1.5 rounded-full uppercase tracking-[0.15em] mb-4 inline-block font-display">My Collection</span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-dark mb-4 uppercase tracking-tight font-display">SẢN PHẨM <span class="text-indigo-600">YÊU THÍCH</span></h1>
        <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full mb-4"></div>
        <p class="text-slate-400 text-xs sm:text-sm font-medium font-sans">Bạn có <span class="font-bold text-slate-800" id="wishlist-count-text"><?= count($wishlistItems) ?></span> sản phẩm trong danh sách yêu thích</p>
    </div>

    <?php if (empty($wishlistItems)): ?>
        <!-- Empty State -->
        <div class="max-w-md mx-auto text-center py-20 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10">
            <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Danh sách yêu thích trống</h3>
            <p class="text-slate-400 text-sm mb-8 leading-relaxed">Hãy khám phá các sản phẩm sneakers đẳng cấp của ShoeStore và thêm chúng vào danh sách của bạn!</p>
            <a href="products.php" class="inline-block btn-gradient text-white font-bold px-8 py-3.5 rounded-full text-sm shadow-lg shadow-indigo-500/20 uppercase tracking-wider">Khám phá ngay</a>
        </div>
    <?php else: ?>
        <!-- Wishlist Grid -->
        <div id="wishlist-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10">
            <?php foreach ($wishlistItems as $item): ?>
                <div id="wishlist-card-<?= $item['id'] ?>" class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 border border-slate-100 group card-hover">
                    <div class="relative pb-[110%] bg-slate-50/50 p-6">
                        <!-- Remove button floating -->
                        <div class="absolute top-5 right-5 z-20">
                            <button onclick="toggleWishlist(<?= $item['id'] ?>, this)" class="w-9 h-9 rounded-full bg-white/80 hover:bg-white backdrop-blur-md flex items-center justify-center text-rose-500 shadow-md transition-all duration-300 hover:scale-110 active:scale-90 select-none" title="Xóa khỏi danh sách yêu thích">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-500 fill-current heart-icon-svg-<?= $item['id'] ?>" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                        
                        <a href="product_detail.php?id=<?= $item['id'] ?>" class="absolute inset-0 z-10"></a>
                        <img src="<?= !empty($item['image_url']) ? 'public' . $item['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80' ?>" class="absolute inset-0 w-full h-full object-contain p-8 mix-blend-multiply group-hover:scale-110 transition-transform duration-500">
                    </div>
                    
                    <div class="p-6 flex flex-col h-full">
                        <div class="mb-3">
                            <p class="premium-brand-tag mb-1.5"><?= htmlspecialchars($item['brand_name'] ?? $item['brand'] ?? '') ?></p>
                            <h3 class="text-sm font-semibold tracking-tight text-slate-800 leading-tight min-h-[44px] line-clamp-2 mb-2">
                                <a href="product_detail.php?id=<?= $item['id'] ?>" class="hover:text-indigo-600 transition-all premium-product-name"><?= htmlspecialchars($item['name']) ?></a>
                            </h3>
                        </div>
                        
                        <div class="mb-5">
                            <span class="text-lg font-extrabold tracking-tight text-slate-900 premium-price"><?= format_price($item['price']) ?></span>
                        </div>
                        
                        <!-- Actions -->
                        <div class="grid grid-cols-5 gap-3 mt-auto">
                            <!-- Add to Cart -->
                            <form action="cart_action.php?action=add" method="POST" class="col-span-4">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="size" value="40">
                                <button type="submit" class="w-full btn-gradient text-white text-xs font-bold py-3.5 px-2 rounded-xl shadow-md flex items-center justify-center gap-1.5 premium-btn tracking-wider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Thêm vào giỏ
                                </button>
                            </form>
                            <!-- Quick Delete Bin -->
                            <button onclick="removeWishlistItem(<?= $item['id'] ?>)" class="w-full rounded-2xl bg-rose-50 hover:bg-rose-100 flex items-center justify-center text-rose-600 transition-colors shadow-sm" title="Xóa">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Specialized secure remove for the trash icon in wishlist page
function removeWishlistItem(productId) {
    const card = document.getElementById(`wishlist-card-${productId}`);
    if (!card) return;
    
    // Add visual feedback indicating removal in progress
    card.classList.add('scale-95', 'opacity-70');
    
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('product_id', productId);
    
    fetch('wishlist_action.php?action=remove', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'unauthorized') {
            showToast(data.message, 'error');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1200);
        } else if (data.status === 'success') {
            // Update navigation badge count
            const badge = document.getElementById('wishlist-badge');
            if (badge) {
                badge.innerText = data.count;
                if (data.count > 0) {
                    badge.classList.remove('scale-0', 'opacity-0');
                    badge.classList.add('scale-100', 'opacity-100');
                } else {
                    badge.classList.remove('scale-100', 'opacity-100');
                    badge.classList.add('scale-0', 'opacity-0');
                }
                
                badge.classList.add('scale-125');
                setTimeout(() => badge.classList.remove('scale-125'), 300);
            }
            
            // Update items count text
            const textCount = document.getElementById('wishlist-count-text');
            if (textCount) {
                textCount.innerText = data.count;
            }

            // De-activate all heart icons matching this product ID on the page
            const heartIcons = document.querySelectorAll(`.heart-icon-svg-${productId}`);
            heartIcons.forEach(svg => {
                svg.setAttribute('fill', 'none');
                svg.classList.remove('text-rose-500');
                svg.classList.add('text-slate-400');
            });

            showToast(data.message, 'success');

            // Smoothly remove card from UI
            card.classList.remove('scale-95', 'opacity-70');
            card.classList.add('opacity-0', 'scale-90');
            setTimeout(() => {
                card.remove();
                
                // If wishlist is completely empty, refresh the page to render the premium empty state
                const grid = document.getElementById('wishlist-grid');
                if (grid && grid.children.length === 0) {
                    location.reload();
                }
            }, 400);
        } else {
            card.classList.remove('scale-95', 'opacity-70');
            showToast(data.message || 'Đã có lỗi xảy ra!', 'error');
        }
    })
    .catch(err => {
        card.classList.remove('scale-95', 'opacity-70');
        console.error('Lỗi khi xóa khỏi danh sách yêu thích:', err);
        showToast('Không thể kết nối đến máy chủ.', 'error');
    });
}
</script>
<?php include 'footer.php'; ?>
