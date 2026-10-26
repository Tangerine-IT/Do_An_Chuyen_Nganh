<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.wishlist', ['products' => $request->user()->wishlists()->with(['product.primaryImage', 'product.brand'])->latest()->paginate(12)]);
    }

    public function toggle(Request $request, Product $product): JsonResponse
    {
        $wishlist = $request->user()->wishlists()->where('product_id', $product->id)->first();
        if ($wishlist) {
            $wishlist->delete();
        } else {
            $request->user()->wishlists()->create(['product_id' => $product->id]);
        }

        return response()->json(['wishlisted' => ! $wishlist]);
    }
}
