<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <span class="current">Shop</span></div>
</div>

<?php $total = $total ?? count($items ?? []); ?>
<?php $filters = $filters ?? []; ?>
<?php $page = $page ?? 1; ?>

<section class="section-sm">
    <div class="container-inner">
        <div class="row g-4">
            <!-- Filters sidebar -->
            <aside class="col-lg-3">
                <form method="GET" action="/products" class="summary-card">
                    <h6 class="mb-3">Filter Products</h6>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach (($categories ?? []) as $cat): ?>
                                <option value="<?= e($cat['id']) ?>" <?= ($filters['category_id'] ?? '') === $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <select name="brand" class="form-select">
                            <option value="">All Brands</option>
                            <?php foreach (($brands ?? []) as $brand): ?>
                                <option value="<?= e($brand['id']) ?>" <?= ($filters['brand_id'] ?? '') === $brand['id'] ? 'selected' : '' ?>><?= e($brand['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price Range (<?= config('app.currency_symbol') ?>)</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?= e($filters['min_price'] ?? '') ?>">
                            <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?= e($filters['max_price'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="inStock" <?= !empty($filters['in_stock']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="inStock">In stock only</label>
                    </div>

                    <input type="hidden" name="q" value="<?= e($filters['q'] ?? '') ?>">
                    <button type="submit" class="btn btn-copper w-100">Apply Filters</button>
                    <a href="/products" class="btn btn-sm w-100 mt-2 text-center text-muted-dtc">Clear all</a>
                </form>
            </aside>

            <!-- Results -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <p class="text-muted-dtc mb-0"><?= $total ?> product<?= $total === 1 ? '' : 's' ?> found<?= !empty($filters['q']) ? ' for "' . e($filters['q']) . '"' : '' ?></p>
                    <form method="GET" action="/products" class="d-flex align-items-center gap-2">
                        <?php foreach ($filters as $k => $v): if ($k === 'sort' || $v === '') continue; ?>
                            <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
                        <?php endforeach; ?>
                        <label class="text-muted-dtc small mb-0">Sort:</label>
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto;">
                            <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Newest</option>
                            <option value="price_low" <?= $filters['sort'] === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $filters['sort'] === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="rating" <?= $filters['sort'] === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                            <option value="name" <?= $filters['sort'] === 'name' ? 'selected' : '' ?>>Name A–Z</option>
                        </select>
                    </form>
                </div>

                <?php if (empty($items)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <h4>No products match your filters</h4>
                        <p>Try widening your search or clearing a few filters.</p>
                        <a href="/products" class="btn btn-copper mt-3">Clear Filters</a>
                    </div>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($items as $product): ?>
                            <?php require resolve_php_file(base_path('views/products'), '_card'); ?>
                        <?php endforeach; ?>
                    </div>

                    <?php $total_pages = $total_pages ?? (int) ceil($total / 12); ?>
                    <?php if ($total_pages > 1): ?>
                        <nav class="dtc-pagination">
                            <?php
                            $qs = $_GET; unset($qs['page']);
                            for ($p = 1; $p <= $total_pages; $p++):
                                $qs['page'] = $p;
                            ?>
                                <a href="/products?<?= http_build_query($qs) ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
                            <?php endfor; ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
