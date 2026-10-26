<?php

namespace App\Http\Controllers;

use App\Models\FeedingEstimate;
use App\Models\Product;
use App\Services\CartService;
use App\Services\FeedingEstimator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('account.feeding', [
            'pets' => $user->pets()->orderBy('name')->get(),
            'products' => Product::query()->active()->whereNotNull('package_size_grams')->whereNotNull('daily_grams_per_kg')->orderBy('name')->get(),
            'estimates' => FeedingEstimate::query()->whereIn('pet_id', $user->pets()->select('id'))->with(['pet', 'product'])->latest()->get(),
        ]);
    }

    public function store(Request $request, FeedingEstimator $estimator): RedirectResponse
    {
        $data = $request->validate(['pet_id' => ['required', 'exists:pets,id'], 'product_id' => ['required', 'exists:products,id']]);
        $pet = $request->user()->pets()->findOrFail($data['pet_id']);
        $product = Product::query()->active()->findOrFail($data['product_id']);
        abort_unless($product->package_size_grams && $product->daily_grams_per_kg && $pet->weight_kg, 422);
        $estimator->estimate($pet, $product);

        return back()->with('success', __('ui.feeding.saved'));
    }

    public function reorder(Request $request, FeedingEstimate $estimate, CartService $cart): RedirectResponse
    {
        abort_unless($request->user()->pets()->whereKey($estimate->pet_id)->exists(), 403);
        $cart->add($cart->current($request->user(), ''), $estimate->product_id, 1);

        return redirect()->route('cart.index')->with('success', __('ui.feeding.reordered'));
    }
}
