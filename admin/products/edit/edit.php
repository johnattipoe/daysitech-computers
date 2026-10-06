<?php

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\InventoryService;

$id = $GLOBALS['adminRouteParams'][0] ?? null;
$product = $id ? Product::find($id) : null;

if (!$product) {
    flash('error', 'Product not found.');
    redirect('/admin/products');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    if (($_POST['_action'] ?? '') === 'delete') {
        Product::delete($id);
        flash('success', 'Product deleted.');
        redirect('/admin/products');
    }

    $images = array_filter(array_map('trim', explode("\n", $_POST['images'] ?? '')));
    $specs = [];
    foreach (($_POST['spec_key'] ?? []) as $i => $k) {
        if (trim($k) !== '') $specs[slugify($k)] = $_POST['spec_value'][$i] ?? '';
    }

    $oldStock = (int) $product['stock'];
    $newStock = (int) $_POST['stock'];

    Product::update($id, [
        'name'              => $_POST['name'],
        'sku'               => $_POST['sku'] ?: $product['sku'],
        'category_id'       => $_POST['category_id'] ?? '',
        'brand_id'          => $_POST['brand_id'] ?? '',
        'price'             => (float) $_POST['price'],
        'compare_price'     => $_POST['compare_price'] !== '' ? (float) $_POST['compare_price'] : null,
        'stock'             => $newStock,
        'short_description' => $_POST['short_description'] ?? '',
        'description'       => $_POST['description'] ?? '',
        'images'            => array_values($images),
        'specs'             => $specs,
        'is_featured'       => isset($_POST['is_featured']),
        'is_active'         => isset($_POST['is_active']),
    ]);

    if ($newStock !== $oldStock) {
        (new InventoryService())->adjust($id, $newStock, 'Manual edit via admin panel');
    }

    flash('success', 'Product updated successfully.');
    redirect('/admin/products/edit/' . $id);
}

$categories = Category::active();
$brands = Brand::active();
$title = 'Edit Product';
ob_start();
?>

<form action="/admin/products/edit/<?= e($id) ?>" method="POST" data-product-editor data-unsaved-warning>
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Basic Information</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" value="<?= e($product['name']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="<?= e($product['sku'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short Description</label>
                        <input type="text" name="short_description" class="form-control" value="<?= e($product['short_description'] ?? '') ?>" maxlength="160">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="5"><?= e($product['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div></div>

            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Images</h6>
                <label class="form-label">Image URLs (one per line)</label>
                <textarea name="images" class="form-control" rows="3"><?= e(implode("\n", $product['images'] ?? [])) ?></textarea>
            </div></div>

            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Specifications</h6>
                <div data-spec-rows>
                <?php $specs = $product['specs'] ?? []; if (empty($specs)) $specs = ['' => '']; ?>
                <?php foreach ($specs as $k => $v): ?>
                    <div class="row g-2 mb-2">
                        <div class="col-5"><input type="text" name="spec_key[]" class="form-control form-control-sm" value="<?= e(ucwords(str_replace('_',' ',$k))) ?>"></div>
                        <div class="col-7"><input type="text" name="spec_value[]" class="form-control form-control-sm" value="<?= e($v) ?>"></div>
                    </div>
                <?php endforeach; ?>
                <div class="row g-2 mb-2">
                    <div class="col-5"><input type="text" name="spec_key[]" class="form-control form-control-sm" placeholder="New spec"></div>
                    <div class="col-7"><input type="text" name="spec_value[]" class="form-control form-control-sm" placeholder="Value"></div>
                </div>
            </div></div>
        </div>

        <div class="col-lg-4">
            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Pricing & Stock</h6>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= e($product['price']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Compare-at Price</label>
                    <input type="number" step="0.01" name="compare_price" class="form-control" value="<?= e($product['compare_price'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="stock" class="form-control" value="<?= (int) $product['stock'] ?>" required>
                </div>
            </div></div>

            <div class="admin-panel"><div class="admin-panel-body">
                <h6 class="mb-3">Organization</h6>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Uncategorized</option>
                        <?php foreach ($categories as $cat): ?><option value="<?= e($cat['id']) ?>" <?= ($product['category_id'] ?? '') === $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">No Brand</option>
                        <?php foreach ($brands as $b): ?><option value="<?= e($b['id']) ?>" <?= ($product['brand_id'] ?? '') === $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="feat" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="feat">Featured product</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="act" <?= !empty($product['is_active']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="act">Active (visible in store)</label>
                </div>
            </div></div>

            <button type="submit" class="btn btn-copper w-100">Update Product</button>
        </div>
    </div>
</form>

<form action="/admin/products/edit/<?= e($id) ?>" method="POST" class="mt-3" data-confirm="Delete this product permanently? This cannot be undone.">
    <?= csrf_field() ?>
    <input type="hidden" name="_action" value="delete">
    <button type="submit" class="btn btn-sm text-danger">Delete Product</button>
</form>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
