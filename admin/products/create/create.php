<?php

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\InventoryService;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $v = \Validator::make($_POST, [
        'name'     => 'required|min:2',
        'price'    => 'required|numeric',
        'stock'    => 'required|numeric',
    ]);

    if ($v->fails()) {
        flash('error', $v->firstError());
        redirect('/admin/products/create');
    }

    $images = array_filter(array_map('trim', explode("\n", $_POST['images'] ?? '')));
    $specs = [];
    $keys = $_POST['spec_key'] ?? [];
    $vals = $_POST['spec_value'] ?? [];
    foreach ($keys as $i => $k) {
        if (trim($k) !== '') $specs[slugify($k)] = $vals[$i] ?? '';
    }

    $product = Product::create([
        'name'              => $_POST['name'],
        'slug'              => slugify($_POST['name']) . '-' . strtolower(substr(uuid(), 0, 5)),
        'sku'               => $_POST['sku'] ?: strtoupper('DTC-' . substr(uuid(), 0, 8)),
        'category_id'       => $_POST['category_id'] ?? '',
        'brand_id'          => $_POST['brand_id'] ?? '',
        'price'             => (float) $_POST['price'],
        'compare_price'     => $_POST['compare_price'] !== '' ? (float) $_POST['compare_price'] : null,
        'stock'             => (int) $_POST['stock'],
        'short_description' => $_POST['short_description'] ?? '',
        'description'       => $_POST['description'] ?? '',
        'images'            => array_values($images),
        'specs'             => $specs,
        'is_featured'       => isset($_POST['is_featured']),
        'is_active'         => isset($_POST['is_active']),
        'rating_avg'        => 0,
        'rating_count'      => 0,
    ]);

    (new InventoryService())->restock($product['id'], (int) $_POST['stock'], 'Initial stock on creation');

    flash('success', 'Product created successfully.');
    redirect('/admin/products');
}

$categories = Category::active();
$brands = Brand::active();
$title = 'Add Product';
ob_start();
?>

<form action="/admin/products/create" method="POST">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Basic Information</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SKU (optional)</label>
                        <input type="text" name="sku" class="form-control" placeholder="Auto-generated">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short Description</label>
                        <input type="text" name="short_description" class="form-control" maxlength="160">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                </div>
            </div></div>

            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Images</h6>
                <label class="form-label">Image URLs (one per line — first is the primary image)</label>
                <textarea name="images" class="form-control" rows="3" placeholder="https://example.com/image1.jpg"></textarea>
            </div></div>

            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Specifications</h6>
                <div id="specRows">
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <div class="row g-2 mb-2">
                            <div class="col-5"><input type="text" name="spec_key[]" class="form-control form-control-sm" placeholder="e.g. Processor"></div>
                            <div class="col-7"><input type="text" name="spec_value[]" class="form-control form-control-sm" placeholder="e.g. Intel Core i7"></div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div></div>
        </div>

        <div class="col-lg-4">
            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Pricing & Stock</h6>
                <div class="mb-3">
                    <label class="form-label">Price (<?= config('app.currency_symbol') ?>)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Compare-at Price</label>
                    <input type="number" step="0.01" name="compare_price" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>
            </div></div>

            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Organization</h6>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Uncategorized</option>
                        <?php foreach ($categories as $cat): ?><option value="<?= e($cat['id']) ?>"><?= e($cat['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">No Brand</option>
                        <?php foreach ($brands as $b): ?><option value="<?= e($b['id']) ?>"><?= e($b['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="feat">
                    <label class="form-check-label" for="feat">Featured product</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="act" checked>
                    <label class="form-check-label" for="act">Active (visible in store)</label>
                </div>
            </div></div>

            <button type="submit" class="btn btn-copper w-100">Save Product</button>
            <a href="/admin/products" class="btn btn-sm w-100 mt-2 text-center text-muted-dtc">Cancel</a>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
