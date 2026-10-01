<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryController
{
    public function index(): void
    {
        view('categories.index', [
            'title'      => 'Shop by Category',
            'categories' => Category::active(),
        ]);
    }

    public function show(string $slug): void
    {
        $category = Category::findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            view('pages.404', ['title' => 'Category Not Found']);
            return;
        }

        redirect('/products?category=' . $category['id']);
    }
}
