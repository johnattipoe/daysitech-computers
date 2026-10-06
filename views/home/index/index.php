<?php
/** @var array<int, array<string, mixed>> $categories */
/** @var array<int, array<string, mixed>> $featured */
/** @var array<int, array<string, mixed>> $newArrivals */
$categories = $categories ?? [];
$featured = $featured ?? [];
$newArrivals = $newArrivals ?? [];
?>

<!-- HERO -->
<section class="hero">
    <div class="container-inner hero-inner">
        <div data-aos="fade-up">
            <span class="eyebrow"><i class="fa-solid fa-bolt"></i> Same-day diagnostics available</span>
            <h1>Computers sold right. Repairs done fast.</h1>
            <p class="lead">Daysitech Computers is Accra's one-stop shop for laptops, desktops and accessories — backed by an in-house repair bench that gets you back online, not back in line.</p>
            <div class="d-flex gap-3 flex-wrap">
                <a href="/products" class="btn btn-copper btn-lg"><i class="fa-solid fa-cart-shopping me-2"></i>Shop Products</a>
                <a href="/repairs/book" class="btn btn-outline-light-line btn-lg"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Book a Repair</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><b>Shop</b><span>Laptops &amp; accessories</span></div>
                <div class="hero-stat"><b>Repair</b><span>Device diagnostics &amp; service</span></div>
                <div class="hero-stat"><b>Support</b><span>Help from our team</span></div>
            </div>
        </div>
        <div class="hero-visual" data-aos="fade-up" data-aos-delay="100">
            <img src="<?= asset('images/banners/hero-device.png') ?>" alt="Laptop on workbench" style="width:100%;border-radius:12px;" onerror="this.style.display='none'">
            <div class="d-flex justify-content-between mt-3" style="font-family:'JetBrains Mono',monospace;color:rgba(255,255,255,.8);font-size:.8rem;">
                <span><i class="fa-solid fa-circle-check" style="color:#2FBE85;"></i> Sales, repairs &amp; support</span>
                <a href="/contact" class="text-reset">Talk to our team <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- TRUST STRIP -->
<div class="trust-strip">
    <div class="container-inner trust-grid">
        <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> Genuine parts & warranty</div>
        <div class="trust-item"><i class="fa-solid fa-truck-fast"></i> Delivery across Accra</div>
        <div class="trust-item"><i class="fa-solid fa-money-bill-wave"></i> Pay on delivery available</div>
        <div class="trust-item"><i class="fa-solid fa-headset"></i> Friendly expert support</div>
    </div>
</div>

<!-- CATEGORIES -->
<section class="section">
    <div class="container-inner">
        <div class="section-head" data-aos="fade-up">
            <div>
                <h2>Shop by Category</h2>
                <p>Find exactly what your setup needs.</p>
            </div>
            <a href="/categories" class="btn btn-ink btn-sm">View All</a>
        </div>
        <div class="category-grid">
            <?php foreach (array_slice($categories, 0, 12) as $cat): ?>
                <a href="/products?category=<?= e($cat['id']) ?>" class="category-tile" data-aos="fade-up">
                    <div class="cat-icon"><i class="fa-solid <?= e($cat['icon'] ?? 'fa-laptop') ?>"></i></div>
                    <div class="cat-name"><?= e($cat['name']) ?></div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <?php foreach ([['Laptops','fa-laptop'],['Desktops','fa-desktop'],['Monitors','fa-tv'],['Printers','fa-print'],['Accessories','fa-keyboard'],['Networking','fa-wifi']] as $c): ?>
                    <a href="/products" class="category-tile" data-aos="fade-up">
                        <div class="cat-icon"><i class="fa-solid <?= $c[1] ?>"></i></div>
                        <div class="cat-name"><?= $c[0] ?></div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- SHOPPING SHORTCUTS -->
<section class="section-sm bg-paper">
    <div class="container-inner">
        <div class="section-head">
            <div><h2>Find a good place to start</h2><p>Explore current stock, compare prices, or browse top-rated products.</p></div>
        </div>
        <div class="row g-3">
            <div class="col-md-4"><a class="home-shopping-shortcut" href="/products?in_stock=1"><i class="fa-solid fa-box-open"></i><span><strong>Available now</strong><small>Browse products currently in stock</small></span><i class="fa-solid fa-arrow-right ms-auto"></i></a></div>
            <div class="col-md-4"><a class="home-shopping-shortcut" href="/products?sort=price_low"><i class="fa-solid fa-tags"></i><span><strong>Shop by price</strong><small>See lower-priced options first</small></span><i class="fa-solid fa-arrow-right ms-auto"></i></a></div>
            <div class="col-md-4"><a class="home-shopping-shortcut" href="/products?sort=rating"><i class="fa-solid fa-star"></i><span><strong>Top rated</strong><small>Browse products with customer ratings</small></span><i class="fa-solid fa-arrow-right ms-auto"></i></a></div>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section bg-paper">
    <div class="container-inner">
        <div class="section-head" data-aos="fade-up">
            <div>
                <h2>Featured Products</h2>
                <p>Hand-picked machines our technicians vouch for.</p>
            </div>
            <a href="/products" class="btn btn-ink btn-sm">Browse Shop</a>
        </div>
        <div class="product-grid">
            <?php foreach ($featured as $product): ?>
                <?php require resolve_php_file(base_path('views/products'), '_card'); ?>
            <?php endforeach; ?>
            <?php if (empty($featured)): ?>
                <div class="empty-state" style="grid-column:1/-1;">
                    <i class="fa-solid fa-box-open"></i>
                    <h4>No featured products yet</h4>
                    <p>Add products in the admin panel and mark them as featured to showcase them here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- REPAIR PROCESS -->
<section class="section">
    <div class="container-inner">
        <div class="section-head" data-aos="fade-up">
            <div>
                <h2>How Our Repair Service Works</h2>
                <p>Four steps from booking to pickup — track every stage online.</p>
            </div>
        </div>
        <div class="process-steps">
            <div class="process-step" data-aos="fade-up">
                <div class="step-num">01</div>
                <h4>Book Online</h4>
                <p>Tell us the device and issue — takes under two minutes.</p>
            </div>
            <div class="process-step" data-aos="fade-up" data-aos-delay="80">
                <div class="step-num">02</div>
                <h4>Free Diagnosis</h4>
                <p>Our technicians inspect the device and send you a quote.</p>
            </div>
            <div class="process-step" data-aos="fade-up" data-aos-delay="160">
                <div class="step-num">03</div>
                <h4>Approve & Repair</h4>
                <p>Approve the quote and we get to work with genuine parts.</p>
            </div>
            <div class="process-step" data-aos="fade-up" data-aos-delay="240">
                <div class="step-num">04</div>
                <h4>Pickup or Delivery</h4>
                <p>We test thoroughly, then notify you it's ready.</p>
            </div>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="/repairs/book" class="btn btn-copper btn-lg">Book Your Repair Now</a>
        </div>
    </div>
</section>

<!-- WHY DAYSITECH -->
<section class="section bg-paper">
    <div class="container-inner">
        <div class="section-head" data-aos="fade-up">
            <div>
                <h2>Everything Your Setup Needs</h2>
                <p>Practical technology support from purchase to repair and beyond.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="summary-card h-100">
                    <div class="cat-icon mb-3"><i class="fa-solid fa-laptop"></i></div>
                    <h3 class="h5">Shop with confidence</h3>
                    <p class="text-muted-dtc mb-0">Compare laptops, desktops, accessories and networking gear selected for real-world use.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="80">
                <div class="summary-card h-100">
                    <div class="cat-icon mb-3"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                    <h3 class="h5">Repair without guesswork</h3>
                    <p class="text-muted-dtc mb-0">Get a clear diagnosis, transparent updates and a repair you can track from booking to pickup.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="160">
                <div class="summary-card h-100">
                    <div class="cat-icon mb-3"><i class="fa-solid fa-headset"></i></div>
                    <h3 class="h5">Support that stays useful</h3>
                    <p class="text-muted-dtc mb-0">From setup advice to after-sales help, our team keeps your devices working for longer.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NEW ARRIVALS -->
<section class="section bg-paper">
    <div class="container-inner">
        <div class="section-head" data-aos="fade-up">
            <div>
                <h2>New Arrivals</h2>
                <p>Freshly stocked and ready to ship.</p>
            </div>
            <a href="/products?sort=newest" class="btn btn-ink btn-sm">See All</a>
        </div>
        <div class="product-grid">
            <?php foreach ($newArrivals as $product): ?>
                <?php require resolve_php_file(base_path('views/products'), '_card'); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
