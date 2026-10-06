<?php
/** @var array<string, mixed> $product */
/** Reusable across home, products/index, categories, and related products. */
$product = $product ?? [];
$inStock = \App\Models\Product::isInStock($product);
$img = $product['images'][0] ?? null;
?>
<div class="product-card">
    <a href="/products/<?= e($product['slug'] ?? $product['id']) ?>" class="product-thumb">
        <?php if ($img): ?>
            <img src="<?= e($img) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
        <?php else: ?>
            <i class="fa-solid fa-laptop" style="font-size:2.5rem;color:#DCE2EC;"></i>
        <?php endif; ?>
        <div class="product-badges">
            <?php if (!empty($product['is_featured'])): ?><span class="badge-pill badge-info">Featured</span><?php endif; ?>
            <?php if (!$inStock): ?><span class="badge-pill badge-danger">Out of Stock</span>
            <?php elseif ((int) $product['stock'] <= LOW_STOCK_THRESHOLD): ?><span class="badge-pill badge-warning">Low Stock</span><?php endif; ?>
        </div>
    </a>
    <button class="wishlist-btn" type="button" aria-label="Save <?= e($product['name']) ?> to wishlist" aria-pressed="false"
        data-wishlist-product
        data-product-id="<?= e($product['id'] ?? '') ?>"
        data-product-name="<?= e($product['name'] ?? '') ?>"
        data-product-url="/products/<?= e($product['slug'] ?? $product['id'] ?? '') ?>"
        data-product-image="<?= e($img ?? '') ?>"
        data-product-price="<?= e((string) ($product['price'] ?? 0)) ?>"
        data-product-price-label="<?= e(strip_tags(money($product['price'] ?? 0))) ?>"
        data-product-in-stock="<?= $inStock ? 'true' : 'false' ?>"><i class="fa-regular fa-heart" aria-hidden="true"></i></button>
    <div class="product-body">
        <div class="product-category"><?= e($product['brand_name'] ?? 'Daysitech') ?></div>
        <a href="/products/<?= e($product['slug'] ?? $product['id']) ?>" class="product-name" style="text-decoration:none;"><?= e($product['name']) ?></a>
        <div class="product-rating">
            <?= star_rating_html((float) ($product['rating_avg'] ?? 0)) ?>
            <span>(<?= (int) ($product['rating_count'] ?? 0) ?>)</span>
        </div>
        <div class="product-price">
            <span class="price-now"><?= money($product['price']) ?></span>
            <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                <span class="price-was"><?= money($product['compare_price']) ?></span>
            <?php endif; ?>
        </div>
        <div class="product-actions">
            <form action="/cart/add" method="POST" class="flex-fill">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                <button type="submit" class="btn btn-copper w-100" <?= $inStock ? '' : 'disabled' ?>>
                    <i class="fa-solid fa-cart-plus me-1"></i><?= $inStock ? 'Add to Cart' : 'Sold Out' ?>
                </button>
            </form>
            <button type="button" class="btn btn-outline-secondary product-compare-toggle" data-compare-product="<?= e($product['id'] ?? '') ?>" aria-pressed="false" aria-label="Compare <?= e($product['name'] ?? 'product') ?>"><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i></button>
        </div>
    </div>
</div>
