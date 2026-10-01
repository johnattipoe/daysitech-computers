<?php
/** @var array<string, mixed> $product */
/** @var array<int, array<string, mixed>> $related */
$product = $product ?? [];
$related = $related ?? [];
$inStock = \App\Models\Product::isInStock($product);
?>
<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <a href="/products">Shop</a> / <span class="current"><?= e($product['name']) ?></span></div>
</div>

<section class="section-sm">
    <div class="container-inner">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="product-thumb" style="aspect-ratio:1;border-radius:16px;border:1px solid #DCE2EC;">
                    <?php if (!empty($product['images'][0])): ?>
                        <img src="<?= e($product['images'][0]) ?>" alt="<?= e($product['name']) ?>" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <i class="fa-solid fa-laptop" style="font-size:5rem;color:#DCE2EC;"></i>
                    <?php endif; ?>
                </div>
                <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
                    <div class="d-flex gap-2 mt-3">
                        <?php foreach (array_slice($product['images'], 1, 4) as $img): ?>
                            <img src="<?= e($img) ?>" style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #DCE2EC;">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-6">
                <div class="product-category mb-2"><?= e($product['brand_name'] ?? 'Daysitech') ?> &middot; SKU: <span class="mono"><?= e($product['sku'] ?? '—') ?></span></div>
                <h1 style="font-size:1.75rem;"><?= e($product['name']) ?></h1>
                <div class="product-rating mb-3"><?= star_rating_html($product['review_avg']) ?> <span><?= $product['review_avg'] ?> (<?= $product['review_count'] ?> reviews)</span></div>

                <div class="d-flex align-items-baseline gap-3 mb-4">
                    <span class="mono" style="font-size:2rem;font-weight:700;color:#0B1F3A;"><?= money($product['price']) ?></span>
                    <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                        <span class="price-was" style="font-size:1.1rem;"><?= money($product['compare_price']) ?></span>
                    <?php endif; ?>
                </div>

                <p class="text-muted-dtc"><?= nl2br(e($product['short_description'] ?? truncate($product['description'] ?? '', 220))) ?></p>

                <div class="mb-4">
                    <?php if ($inStock): ?>
                        <span class="badge-pill badge-success"><i class="fa-solid fa-check"></i> In Stock (<?= (int) $product['stock'] ?> available)</span>
                    <?php else: ?>
                        <span class="badge-pill badge-danger">Out of Stock</span>
                    <?php endif; ?>
                </div>

                <form action="/cart/add" method="POST" class="d-flex gap-3 align-items-center mb-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                    <div class="qty-stepper">
                        <button type="button" onclick="this.nextElementSibling.stepDown()">−</button>
                        <input type="number" name="qty" value="1" min="1" max="<?= (int) $product['stock'] ?>">
                        <button type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                    </div>
                    <button type="submit" class="btn btn-copper btn-lg flex-fill" <?= $inStock ? '' : 'disabled' ?>>
                        <i class="fa-solid fa-cart-plus me-2"></i><?= $inStock ? 'Add to Cart' : 'Sold Out' ?>
                    </button>
                </form>

                <div class="d-flex gap-4 text-muted-dtc small">
                    <span><i class="fa-solid fa-shield-halved me-1"></i> 6-month warranty</span>
                    <span><i class="fa-solid fa-truck-fast me-1"></i> Delivery in Accra</span>
                    <span><i class="fa-solid fa-rotate-left me-1"></i> 7-day returns</span>
                </div>
            </div>
        </div>

        <!-- Tabs: Description / Specs / Reviews -->
        <div class="mt-5">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-desc">Description</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-specs">Specifications</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reviews">Reviews (<?= $product['review_count'] ?>)</button></li>
            </ul>
            <div class="tab-content bg-paper p-4 border border-top-0">
                <div class="tab-pane fade show active" id="tab-desc">
                    <p><?= nl2br(e($product['description'] ?? 'No description available.')) ?></p>
                </div>
                <div class="tab-pane fade" id="tab-specs">
                    <?php if (!empty($product['specs'])): ?>
                        <table class="table-dtc w-100">
                            <?php foreach ($product['specs'] as $key => $value): ?>
                                <tr><th style="width:220px;"><?= e(ucwords(str_replace('_', ' ', $key))) ?></th><td><?= e($value) ?></td></tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <p class="text-muted-dtc">No specifications listed for this product yet.</p>
                    <?php endif; ?>
                </div>
                <div class="tab-pane fade" id="tab-reviews">
                    <?php foreach ($product['reviews'] as $review): ?>
                        <div class="mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between">
                                <strong><?= e($review['customer_name']) ?></strong>
                                <span class="text-muted-dtc small"><?= time_ago($review['created_at']) ?></span>
                            </div>
                            <div class="product-rating my-1"><?= star_rating_html($review['rating']) ?></div>
                            <p class="mb-0"><?= e($review['comment']) ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($product['reviews'])): ?>
                        <p class="text-muted-dtc">No reviews yet — be the first to review this product.</p>
                    <?php endif; ?>

                    <?php if (is_logged_in()): ?>
                        <form action="/reviews" method="POST" class="mt-4">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                            <div class="mb-3">
                                <label class="form-label">Your Rating</label>
                                <select name="rating" class="form-select" style="width:140px;" required>
                                    <option value="5">★★★★★</option>
                                    <option value="4">★★★★☆</option>
                                    <option value="3">★★★☆☆</option>
                                    <option value="2">★★☆☆☆</option>
                                    <option value="1">★☆☆☆☆</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Your Review</label>
                                <textarea name="comment" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-copper">Submit Review</button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted-dtc mt-3"><a href="/login">Sign in</a> to leave a review.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Related products -->
        <?php if (!empty($related)): ?>
        <div class="mt-5">
            <div class="section-head"><h2>You Might Also Like</h2></div>
            <div class="product-grid">
                <?php foreach ($related as $relatedProduct): ?>
                    <?php $product = $relatedProduct; ?>
                    <?php require base_path('views/products/_card.php'); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
