<?php

use App\Models\Category;
use App\Models\Brand;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $type = $_POST['type'] ?? '';

    if (($_POST['_action'] ?? '') === 'delete') {
        $type === 'brand' ? Brand::delete($_POST['id']) : Category::delete($_POST['id']);
        flash('success', ucfirst($type) . ' deleted.');
        redirect('/admin/products/categories');
    }

    if ($type === 'category') {
        Category::create([
            'name' => $_POST['name'],
            'slug' => slugify($_POST['name']),
            'icon' => $_POST['icon'] ?: 'fa-laptop',
            'is_active' => true,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ]);
        flash('success', 'Category added.');
    } elseif ($type === 'brand') {
        Brand::create(['name' => $_POST['name'], 'slug' => slugify($_POST['name']), 'is_active' => true]);
        flash('success', 'Brand added.');
    }

    redirect('/admin/products/categories');
}

$categories = Category::all(200);
$brands = Brand::all(200);
$title = 'Categories & Brands';
ob_start();
?>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Categories</h3></div>
            <div class="admin-panel-body">
                <form action="/admin/products/categories" method="POST" class="row g-2 mb-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="category">
                    <div class="col-5"><input type="text" name="name" class="form-control form-control-sm" placeholder="Category name" required></div>
                    <div class="col-4"><input type="text" name="icon" class="form-control form-control-sm" placeholder="fa-laptop"></div>
                    <div class="col-3"><button class="btn btn-copper btn-sm w-100">Add</button></div>
                </form>
                <table class="table-dtc w-100">
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td><i class="fa-solid <?= e($c['icon'] ?? 'fa-laptop') ?> me-2"></i><?= e($c['name']) ?></td>
                            <td style="text-align:right;">
                                <form action="/admin/products/categories" method="POST" data-confirm="Delete this category?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="type" value="category">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($c['id']) ?>">
                                    <button class="btn btn-sm text-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?><tr><td class="text-muted-dtc">No categories yet.</td></tr><?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Brands</h3></div>
            <div class="admin-panel-body">
                <form action="/admin/products/categories" method="POST" class="row g-2 mb-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="brand">
                    <div class="col-9"><input type="text" name="name" class="form-control form-control-sm" placeholder="Brand name" required></div>
                    <div class="col-3"><button class="btn btn-copper btn-sm w-100">Add</button></div>
                </form>
                <table class="table-dtc w-100">
                    <?php foreach ($brands as $b): ?>
                        <tr>
                            <td><?= e($b['name']) ?></td>
                            <td style="text-align:right;">
                                <form action="/admin/products/categories" method="POST" data-confirm="Delete this brand?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="type" value="brand">
                                    <input type="hidden" name="_action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($b['id']) ?>">
                                    <button class="btn btn-sm text-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($brands)): ?><tr><td class="text-muted-dtc">No brands yet.</td></tr><?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
