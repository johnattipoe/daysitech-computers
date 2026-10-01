<div class="page-header">
    <div class="container-inner">
        <h1>Shop by Category</h1>
        <p>Browse our full range of computer hardware and accessories.</p>
    </div>
</div>

<section class="section">
    <div class="container-inner">
        <?php $categories = $categories ?? []; ?>
        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-folder-open"></i>
                <h4>No categories yet</h4>
                <p>Add categories from the admin panel to organize your catalog.</p>
            </div>
        <?php else: ?>
            <div class="category-toolbar">
                <div>
                    <p class="category-kicker">CATALOG / INDEX</p>
                    <h2>Choose a department</h2>
                    <p class="category-intro">Find the right section and browse its collection.</p>
                </div>
                <label class="category-search">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <span class="visually-hidden">Search categories</span>
                    <input id="categorySearch" type="search" placeholder="Search categories" autocomplete="off">
                </label>
            </div>
            <p class="category-result-count" id="categoryResultCount" role="status" aria-live="polite"><?= count($categories) ?> <?= count($categories) === 1 ? 'category' : 'categories' ?></p>
            <div class="category-grid" id="categoryGrid">
                <?php foreach ($categories as $cat): ?>
                    <a href="/products?category=<?= e($cat['id']) ?>" class="category-tile" data-category-card data-category-name="<?= e(mb_strtolower((string) ($cat['name'] ?? ''))) ?>">
                        <span class="cat-icon"><i class="fa-solid <?= e($cat['icon'] ?? 'fa-laptop') ?>" aria-hidden="true"></i></span>
                        <span class="category-tile-copy">
                            <span class="cat-name"><?= e($cat['name'] ?? 'Category') ?></span>
                            <span class="category-tile-action">Explore collection</span>
                        </span>
                        <span class="category-tile-arrow"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="category-no-results" id="categoryNoResults" hidden>
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <p>No categories match that search.</p>
                <button class="category-clear-search" id="categoryClearSearch" type="button">Clear search</button>
            </div>
        <?php endif; ?>
    </div>
</section>
