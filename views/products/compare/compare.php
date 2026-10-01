<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <a href="/products">Shop</a> / <span class="current">Compare</span></div>
</div>

<section class="section-sm">
    <div class="container-inner">
        <div class="section-head"><h2>Compare Products</h2></div>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-scale-balanced"></i>
                <h4>No products selected</h4>
                <p>Pick a couple of products from the shop to compare specs side by side.</p>
                <a href="/products" class="btn btn-copper mt-3">Browse Products</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table-dtc w-100">
                <tr>
                    <th style="width:200px;">Product</th>
                    <?php foreach ($products as $p): ?>
                        <td style="text-align:center;">
                            <img src="<?= e($p['images'][0] ?? '') ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;" onerror="this.style.display='none'">
                            <div class="fw-bold mt-2"><?= e($p['name']) ?></div>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <th>Price</th>
                    <?php foreach ($products as $p): ?><td style="text-align:center;" class="mono fw-bold"><?= money($p['price']) ?></td><?php endforeach; ?>
                </tr>
                <tr>
                    <th>Stock</th>
                    <?php foreach ($products as $p): ?><td style="text-align:center;"><?= \App\Models\Product::isInStock($p) ? 'In Stock' : 'Out of Stock' ?></td><?php endforeach; ?>
                </tr>
                <?php
                $specKeys = [];
                foreach ($products as $p) { $specKeys = array_merge($specKeys, array_keys($p['specs'] ?? [])); }
                $specKeys = array_unique($specKeys);
                ?>
                <?php foreach ($specKeys as $key): ?>
                    <tr>
                        <th><?= e(ucwords(str_replace('_', ' ', $key))) ?></th>
                        <?php foreach ($products as $p): ?>
                            <td style="text-align:center;"><?= e($p['specs'][$key] ?? '—') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th></th>
                    <?php foreach ($products as $p): ?>
                        <td style="text-align:center;">
                            <a href="/products/<?= e($p['slug'] ?? $p['id']) ?>" class="btn btn-copper btn-sm">View Product</a>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>
