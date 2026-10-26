<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Service;
use App\Services\RecommendationService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(RecommendationService $recommendations): View
    {
        $currentPet = auth()->user()?->isCustomer()
            ? auth()->user()->pets()->find(session('current_pet_id'))
            : null;

        return view('home', [
            'categories' => Category::query()->active()->roots()->orderBy('sort_order')->get(),
            'featuredProducts' => Product::query()
                ->active()
                ->inStock()
                ->featured()
                ->with(['primaryImage', 'brand'])
                ->withRatingSummary()
                ->latest('id')
                ->limit(8)
                ->get(),
            'services' => Service::query()->active()->orderBy('id')->limit(4)->get(),
            'posts' => Post::query()->published()->latest('published_at')->limit(3)->get(),
            'currentPet' => $currentPet,
            'recommendedProducts' => $currentPet ? $recommendations->recommended($currentPet) : collect(),
        ]);
    }
}
