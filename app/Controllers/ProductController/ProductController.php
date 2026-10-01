<?php

namespace App\Controllers;

use App\Models\Product;
use App\Services\ProductService;

class ProductController
{
    protected ProductService $products;

    public function __construct()
    {
        $this->products = new ProductService();
    }

    public function index(): void
    {
        $filters = [
            'q'           => $_GET['q'] ?? '',
            'category_id' => $_GET['category'] ?? '',
            'brand_id'    => $_GET['brand'] ?? '',
            'min_price'   => $_GET['min_price'] ?? '',
            'max_price'   => $_GET['max_price'] ?? '',
            'in_stock'    => $_GET['in_stock'] ?? '',
            'sort'        => $_GET['sort'] ?? 'newest',
        ];

        $results = $this->products->search($filters);
        $page = (int) ($_GET['page'] ?? 1);
        $paged = $this->products->paginate($results, $page, config('app.pagination.products_per_page'));

        view('products.index', array_merge([
            'title'      => 'Shop Products',
            'filters'    => $filters,
            'pageScript' => 'products.js',
        ], $paged, $this->products->filterOptions()));
    }

    public function search(): void
    {
        $this->index();
    }

    public function details(string $slug): void
    {
        $product = Product::findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            view('pages.404', ['title' => 'Product Not Found']);
            return;
        }

        $product = $this->products->withReviewSummary($product);

        view('products.details', [
            'title'      => $product['name'],
            'product'    => $product,
            'related'    => $this->products->related($product),
            'pageScript' => 'products.js',
        ]);
    }

    public function compare(): void
    {
        $ids = array_filter(explode(',', $_GET['ids'] ?? ''));
        $products = array_filter(array_map(fn($id) => Product::find($id), $ids));

        view('products.compare', [
            'title'    => 'Compare Products',
            'products' => $products,
        ]);
    }
}
