<?php 
$title = 'Trang chủ - ShoeStore';
include 'header.php'; 

// Lấy sản phẩm nổi bật 
$stmt = $pdo->prepare("
    SELECT p.*, pi.image_url 
    FROM products p 
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
    WHERE p.category_id IS NULL OR p.category_id != (SELECT id FROM categories WHERE slug = 'shoelaces' OR name = 'Dây giày' LIMIT 1)
    ORDER BY p.created_at DESC LIMIT 4
");
$stmt->execute();
$featuredProducts = $stmt->fetchAll();

// Lấy danh sách sản phẩm đã thích của user
$userWishlist = [];
if (isset($_SESSION['user_id'])) {
    $stmt_wl_ids = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stmt_wl_ids->execute([$_SESSION['user_id']]);
    $userWishlist = $stmt_wl_ids->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!-- Hero Section -->
<div class="relative bg-slate-900 min-h-[600px] lg:h-[650px] py-16 lg:py-0 flex items-center overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute top-0 left-0 w-full h-full">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[60%] bg-indigo-600/20 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[60%] bg-blue-600/20 blur-[120px] rounded-full"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="w-full lg:w-1/2 text-center lg:text-left">
                <span class="inline-block px-4 py-1.5 bg-indigo-600/10 border border-indigo-500/20 rounded-full text-indigo-400 text-xs font-bold tracking-[0.18em] uppercase mb-6 animate-bounce font-display"><?= __('new_season') ?></span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-[-0.02em] mb-6 font-display">
    <?= __('hero_title') ?>
</h1>
                <p class="text-base sm:text-lg text-slate-400 mb-10 max-w-lg mx-auto lg:mx-0 leading-relaxed font-sans"><?= __('hero_subtitle') ?></p>
                <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                    <a href="products.php" class="btn-gradient text-white font-bold px-10 py-4 rounded-full text-sm uppercase tracking-[0.1em] premium-btn shadow-xl shadow-indigo-500/20"><?= __('buy_now') ?></a>
                </div>
            </div>
            <div class="w-full lg:w-1/2 relative">
                <div class="relative z-10 animate-[float_6s_ease-in-out_infinite]">
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Nike Air" class="w-full drop-shadow-[0_35px_35px_rgba(99,102,241,0.5)] rotate-[-15deg]">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-12 bg-white border-b border-slate-100">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="flex items-center gap-4 p-6 rounded-2xl hover:bg-slate-50 transition-colors">
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-dark uppercase tracking-[0.12em] font-display mb-0.5"><?= __('shipping_free') ?></h4>
                    <p class="text-slate-400 text-xs font-medium font-sans">On orders over $100</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-6 rounded-2xl hover:bg-slate-50 transition-colors">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-dark uppercase tracking-[0.12em] font-display mb-0.5"><?= __('genuine') ?></h4>
                    <p class="text-slate-400 text-xs font-medium font-sans">Authentic Products Only</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-6 rounded-2xl hover:bg-slate-50 transition-colors">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-dark uppercase tracking-[0.12em] font-display mb-0.5"><?= __('support') ?></h4>
                    <p class="text-slate-400 text-xs font-medium font-sans">Professional Support</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Grid -->
<div class="py-20 bg-slate-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-extrabold uppercase tracking-tight text-dark mb-3 font-display"><?= __('featured_categories') ?></h2>
            <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Men -->
            <div class="group relative h-[450px] rounded-3xl overflow-hidden shadow-xl">
                <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-8">
                    <h3 class="text-2xl font-extrabold text-white mb-1.5 tracking-wider font-display uppercase">MENS</h3>
                    <p class="text-slate-300 mb-4 text-xs font-medium tracking-wide">Bold Steps Every Day</p>
                    <a href="products.php?category=men" class="inline-flex items-center gap-2 text-white text-xs font-semibold tracking-wider uppercase group-hover:gap-3 transition-all">DISCOVER NOW <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg></a>
                </div>
            </div>
            <!-- Women -->
            <div class="group relative h-[450px] rounded-3xl overflow-hidden shadow-xl lg:mt-12">
                <img src="https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-indigo-900/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-8">
                    <h3 class="text-2xl font-extrabold text-white mb-1.5 tracking-wider font-display uppercase">WOMENS</h3>
                    <p class="text-slate-300 mb-4 text-xs font-medium tracking-wide">Elegance & Style</p>
                    <a href="products.php?category=women" class="inline-flex items-center gap-2 text-white text-xs font-semibold tracking-wider uppercase group-hover:gap-3 transition-all">DISCOVER NOW <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg></a>
                </div>
            </div>
            <!-- Basketball -->
            <div class="group relative h-[450px] rounded-3xl overflow-hidden shadow-xl">
                <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-8">
                    <h3 class="text-2xl font-extrabold text-white mb-1.5 tracking-wider font-display uppercase">BASKETBALL</h3>
                    <p class="text-slate-300 mb-4 text-xs font-medium tracking-wide">Master The Court</p>
                    <a href="products.php?category=sport" class="inline-flex items-center gap-2 text-white text-xs font-semibold tracking-wider uppercase group-hover:gap-3 transition-all">DISCOVER NOW <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products Grid -->
<div class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div class="max-w-2xl text-center md:text-left">
                <h2 class="text-3xl md:text-4xl font-extrabold uppercase tracking-tight text-dark mb-3 font-display"><?= __('best_seller') ?></h2>
                <p class="text-slate-400 font-medium text-xs sm:text-sm tracking-wider uppercase font-sans">Top trending picks of the month.</p>
            </div>
            <a href="products.php" class="px-8 py-3 bg-slate-900 text-white rounded-full font-bold hover:bg-indigo-600 transition-all flex items-center gap-2 text-xs uppercase tracking-widest premium-btn"><?= __('all_products') ?> <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <?php foreach($featuredProducts as $product): ?>
            <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all border border-slate-100 group card-hover">
                <div class="relative pb-[110%] bg-slate-50/50 p-6">
                    <div class="absolute top-5 left-5 z-20">
                        <span class="bg-indigo-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">New</span>
                    </div>
                    <!-- Heart Wishlist button -->
                    <div class="absolute top-5 right-5 z-20">
                        <?php 
                        $isFavorited = in_array($product['id'], $userWishlist);
                        ?>
                        <button onclick="event.preventDefault(); toggleWishlist(<?= $product['id'] ?>, this)" class="w-9 h-9 rounded-full bg-white/80 hover:bg-white backdrop-blur-md flex items-center justify-center text-slate-700 shadow-md transition-all duration-300 hover:scale-110 active:scale-90 select-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-colors duration-300 heart-icon-svg-<?= $product['id'] ?> <?= $isFavorited ? 'text-rose-500 fill-current animate-heart-pop' : 'text-slate-400 hover:text-rose-500' ?>" fill="<?= $isFavorited ? 'currentColor' : 'none' ?>" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                    <a href="product_detail.php?id=<?= $product['id'] ?>" class="absolute inset-0 z-10"></a>
                    <img src="<?= !empty($product['image_url']) ? 'public' . $product['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80' ?>" class="absolute inset-0 w-full h-full object-contain p-8 mix-blend-multiply group-hover:scale-110 transition-transform duration-500">
                    
                    <div class="absolute inset-x-6 bottom-6 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 z-20">
                        <form action="cart_action.php?action=add" method="POST" class="w-full">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" class="w-full btn-gradient text-white text-xs font-bold py-3.5 px-5 rounded-xl shadow-lg flex items-center justify-center gap-2 premium-btn tracking-wider">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <?= __('add_to_cart') ?>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2.5">
                        <p class="premium-brand-tag"><?= htmlspecialchars($product['brand']) ?></p>
                        <div class="flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100">
                            <svg class="w-2.5 h-2.5 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span class="text-[9px] font-bold text-slate-500">4.8</span>
                        </div>
                    </div>
                    <h3 class="text-sm font-semibold tracking-tight text-slate-800 mb-3.5 leading-snug">
                        <a href="product_detail.php?id=<?= $product['id'] ?>" class="hover:text-indigo-600 transition-all line-clamp-2 premium-product-name"><?= htmlspecialchars($product['name']) ?></a>
                    </h3>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-extrabold tracking-tight text-slate-900 premium-price"><?= format_price($product['price']) ?></span>
                        <div class="flex -space-x-1.5">
                            <div class="w-5 h-5 rounded-full bg-slate-900 border border-white"></div>
                            <div class="w-5 h-5 rounded-full bg-indigo-600 border border-white"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Promotion Section -->
<div class="container mx-auto px-4 py-24">
    <div class="bg-indigo-600 rounded-[3rem] overflow-hidden relative p-12 sm:p-20 shadow-2xl shadow-indigo-200">
        <div class="absolute top-0 right-0 w-full h-full">
            <div class="absolute top-[-50%] right-[-10%] w-[80%] h-[150%] bg-indigo-400/20 blur-[100px] rounded-full rotate-45"></div>
        </div>
        <div class="relative z-10 flex flex-col lg:flex-row items-center gap-12">
            <div class="w-full lg:w-3/5 text-center lg:text-left">
                <h2 class="text-4xl sm:text-6xl font-black text-white mb-6"><?= __('newsletter_title') ?></h2>
                <p class="text-indigo-100 text-lg mb-10 max-w-xl mx-auto lg:mx-0">Join the ShoeStore community to never miss out on limited releases and exclusive vouchers.</p>
                <form action="subscribe_action.php" method="POST" class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto lg:mx-0">
                    <input type="email" name="email" required placeholder="Your email here..." class="flex-1 bg-white/10 border border-white/20 rounded-full px-6 py-4 text-white placeholder-indigo-200 focus:outline-none focus:ring-2 focus:ring-white/50 backdrop-blur-md">
                    <button type="submit" class="bg-white text-indigo-600 font-bold px-8 py-4 rounded-full hover:bg-slate-100 transition-all"><?= __('newsletter_btn') ?></button>
                </form>
            </div>
            <div class="w-full lg:w-2/5">
                <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full rounded-2xl shadow-2xl rotate-[5deg] group-hover:rotate-0 transition-transform duration-500">
            </div>
        </div>
    </div>
</div>

<style>
@keyframes float {
    0% { transform: translateY(0px) rotate(-15deg); }
    50% { transform: translateY(-20px) rotate(-12deg); }
    100% { transform: translateY(0px) rotate(-15deg); }
}
</style>

<?php include 'footer.php'; ?>
