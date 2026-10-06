<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Repair;

class HomeController
{
    public function index(): void
    {
        view('home.index', [
            'title'      => 'Daysitech Computers — Sales, Repairs & IT Solutions',
            'pageScript' => 'products.js',
            'featured'   => Product::featured(8),
            'categories' => Category::active(),
            'newArrivals' => Product::active(8),
        ]);
    }

    public function about(): void
    {
        $approvedReviews = array_values(array_filter(Review::all(500), fn(array $review) => !empty($review['is_approved'])));
        view('pages.about', [
            'title' => 'About Us',
            'aboutStats' => [
                'products' => count(Product::active(500)),
                'repairs' => count(Repair::all(500)),
                'reviews' => count($approvedReviews),
                'rating' => Review::averageRating($approvedReviews),
            ],
        ]);
    }

    public function contact(): void
    {
        view('pages.contact', ['title' => 'Contact Us']);
    }

    public function submitContact(): void
    {
        require_csrf();
        // In production: persist to a "messages" collection + email the shop.
        flash('success', "Thanks {$_POST['name']}! We've received your message and will respond shortly.");
        redirect('/contact');
    }

    public function terms(): void
    {
        view('pages.terms', ['title' => 'Terms of Service']);
    }

    public function privacy(): void
    {
        view('pages.privacy', ['title' => 'Privacy Policy']);
    }
}
