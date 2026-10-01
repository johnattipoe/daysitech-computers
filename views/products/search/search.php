<?php
/** @var array<int, array<string, mixed>> $items */
/** @var array<string, mixed> $filters */
/** @var array<int, array<string, mixed>> $categories */
/** @var array<int, array<string, mixed>> $brands */
$items = $items ?? [];
$filters = $filters ?? [];
$categories = $categories ?? [];
$brands = $brands ?? [];
$query = trim((string) ($filters['q'] ?? $_GET['q'] ?? ''));
$total = (int) ($total ?? count($items));
$page = max(1, (int) ($page ?? 1));
$totalPages = max(1, (int) ($total_pages ?? ceil($total / max(1, (int) ($per_page ?? 12)))));
?>

<div class="dtc-breadcrumb">
	<div class="container-inner">
		<a href="/">Home</a> / <a href="/products">Shop</a> / <span class="current">Search</span>
	</div>
</div>

<section class="section-sm">
	<div class="container-inner">
		<div class="page-header mb-4">
			<div>
				<h1>Search Products</h1>
				<p><?= $query !== '' ? 'Results for "' . e($query) . '"' : 'Find the right computer for your setup.' ?></p>
			</div>
		</div>

		<form method="GET" action="/products/search" class="summary-card mb-5">
			<div class="row g-3 align-items-end">
				<div class="col-lg-6">
					<label class="form-label" for="productSearch">Search catalog</label>
					<input id="productSearch" type="search" name="q" class="form-control" value="<?= e($query) ?>" placeholder="Search laptops, parts, brands..." autofocus>
				</div>
				<div class="col-md-3 col-lg-2">
					<label class="form-label" for="searchCategory">Category</label>
					<select id="searchCategory" name="category" class="form-select">
						<option value="">All categories</option>
						<?php foreach ($categories as $category): ?>
							<option value="<?= e($category['id']) ?>" <?= ($filters['category_id'] ?? '') === $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-3 col-lg-2">
					<label class="form-label" for="searchSort">Sort by</label>
					<select id="searchSort" name="sort" class="form-select">
						<?php foreach (['newest' => 'Newest', 'price_low' => 'Price: Low to High', 'price_high' => 'Price: High to Low', 'rating' => 'Top Rated', 'name' => 'Name A-Z'] as $value => $label): ?>
							<option value="<?= $value ?>" <?= ($filters['sort'] ?? 'newest') === $value ? 'selected' : '' ?>><?= $label ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-lg-2">
					<button type="submit" class="btn btn-copper w-100"><i class="fa-solid fa-magnifying-glass me-1"></i>Search</button>
				</div>
			</div>
		</form>

		<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
			<p class="text-muted-dtc mb-0"><?= $total ?> result<?= $total === 1 ? '' : 's' ?> found</p>
			<?php if ($query !== '' || !empty($filters['category_id'])): ?>
				<a href="/products/search" class="btn btn-sm text-muted-dtc">Clear search</a>
			<?php endif; ?>
		</div>

		<?php if (empty($items)): ?>
			<div class="empty-state">
				<i class="fa-solid fa-magnifying-glass"></i>
				<h4>No matching products</h4>
				<p>Try a different product name, brand, or category.</p>
				<a href="/products" class="btn btn-copper mt-3">Browse All Products</a>
			</div>
		<?php else: ?>
			<div class="product-grid">
				<?php foreach ($items as $product): ?>
					<?php require resolve_php_file(base_path('views/products'), '_card'); ?>
				<?php endforeach; ?>
			</div>

			<?php if ($totalPages > 1): ?>
				<nav class="dtc-pagination" aria-label="Search results pages">
					<?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>
						<?php $queryParams = array_filter(array_merge($filters, ['page' => $pageNumber]), static fn($value) => $value !== ''); ?>
						<a href="/products/search?<?= http_build_query($queryParams) ?>" class="<?= $pageNumber === $page ? 'active' : '' ?>" aria-label="Page <?= $pageNumber ?>" <?= $pageNumber === $page ? 'aria-current="page"' : '' ?>><?= $pageNumber ?></a>
					<?php endfor; ?>
				</nav>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
