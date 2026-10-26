<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\RecommendationService;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        return view('catalog.index', [
            'title' => __('ui.catalog.title'),
            'description' => __('ui.catalog.description'),
        ]);
    }

    public function category(Category $category): View
    {
        abort_unless($category->is_active, 404);

        return view('catalog.index', [
            'title' => $category->name,
            'description' => $category->description ?: __('ui.catalog.category_description', ['category' => $category->name]),
            'categoryId' => $category->id,
        ]);
    }

    public function brand(Brand $brand): View
    {
        abort_unless($brand->is_active, 404);

        return view('catalog.index', [
            'title' => $brand->name,
            'description' => $brand->description ?: __('ui.catalog.brand_description', ['brand' => $brand->name]),
            'brandId' => $brand->id,
        ]);
    }

    public function show(string $slug, RecommendationService $recommendations): View
    {
        $product = Product::query()
            ->active()
            ->with(['images', 'brand', 'category'])
            ->withRatingSummary()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->active()
            ->inStock()
            ->whereKeyNot($product->id)
            ->where(function ($query) use ($product): void {
                $query->where('category_id', $product->category_id)
                    ->orWhere('species', $product->species->value);
            })
            ->with(['primaryImage', 'brand'])
            ->withRatingSummary()
            ->latest('id')
            ->limit(4)
            ->get();
        $reviews = $product->reviews()->approved()->with('user')->latest()->paginate(5, ['*'], 'reviews_page');
        $reviewDistribution = $product->reviews()->approved()->selectRaw('rating, count(*) as total')->groupBy('rating')->pluck('total', 'rating');
        $currentPet = auth()->user()?->isCustomer()
            ? auth()->user()->pets()->find(session('current_pet_id'))
            : null;

        return view('catalog.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews,
            'reviewDistribution' => $reviewDistribution,
            'currentPet' => $currentPet,
            'recommendation' => $currentPet ? $recommendations->explain($product, $currentPet) : null,
        ]);
    }
}
