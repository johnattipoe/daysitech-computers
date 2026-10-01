<?php

namespace App\Controllers;

use App\Models\Review;
use App\Models\Product;

class ReviewController
{
    public function store(): void
    {
        require_csrf();

        $v = \Validator::make($_POST, [
            'product_id' => 'required',
            'rating'     => 'required|numeric',
            'comment'    => 'required|min:5',
        ]);

        if ($v->fails()) {
            flash('error', $v->firstError());
            redirect($_SERVER['HTTP_REFERER'] ?? '/products');
        }

        $user = current_user();

        Review::create([
            'product_id'    => $_POST['product_id'],
            'user_id'       => $user['id'] ?? null,
            'customer_name' => $user['name'] ?? ($_POST['name'] ?? 'Anonymous'),
            'rating'        => (int) $_POST['rating'],
            'title'         => $_POST['title'] ?? '',
            'comment'       => $_POST['comment'],
            'is_approved'   => !config('app.features.reviews_require_approval'),
        ]);

        flash('success', config('app.features.reviews_require_approval')
            ? 'Thanks! Your review will appear once approved.'
            : 'Thanks for your review!');

        redirect($_SERVER['HTTP_REFERER'] ?? '/products');
    }

    public function destroy(string $id): void
    {
        require_csrf();
        Review::delete($id);
        flash('success', 'Review removed.');
        redirect($_SERVER['HTTP_REFERER'] ?? '/admin/reviews');
    }
}
