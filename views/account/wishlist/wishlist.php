<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <a href="/account/dashboard">Dashboard</a> / <span class="current">Wishlist</span></div>
</div>

<section class="section-sm">
    <div class="container-inner">
        <div class="row g-4">
            <div class="col-lg-3"><?php require base_path('views/account/_sidebar.php'); ?></div>
            <div class="col-lg-9">
                <h1 class="mb-4" style="font-size:1.5rem;">My Wishlist</h1>
                <p class="text-muted-dtc small mb-3">Wishlist items are saved in this browser.</p>
                <div class="product-grid" data-wishlist-grid aria-live="polite"></div>
                <div class="empty-state" data-wishlist-empty>
                    <i class="fa-regular fa-heart"></i>
                    <h4>Your wishlist is empty</h4>
                    <p>Save products with the heart button while browsing the shop. They will appear here on this browser.</p>
                    <a href="/products" class="btn btn-copper mt-3">Browse Products</a>
                </div>
            </div>
        </div>
    </div>
</section>
