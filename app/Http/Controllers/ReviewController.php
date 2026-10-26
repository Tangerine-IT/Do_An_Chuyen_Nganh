<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);
        $user = $request->user();
        abort_unless($user->orders()->where('status', OrderStatus::Completed)->whereHas('items', fn ($items) => $items->where('product_id', $product->id))->exists(), 403);
        abort_if($user->reviews()->where('product_id', $product->id)->exists(), 422, __('ui.review.already_reviewed'));

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('reviews', 'public');
        }
        unset($data['photo']);
        $review = new Review($data);
        $review->user_id = $user->id;
        $review->product_id = $product->id;
        $review->save();

        return back()->with('success', __('ui.review.saved'));
    }
}
