<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/**
 * Component gallery for reviewing the design system. Routes are only registered in the local environment.
 */
class DesignController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->active()
            ->with(['primaryImage', 'brand'])
            ->withRatingSummary()
            ->orderByRaw('sale_price IS NULL')
            ->orderBy('id')
            ->limit(4)
            ->get();

        return view('design.index', [
            'products' => $products,
            'paginator' => new LengthAwarePaginator(range(1, 10), 87, 10, 3, ['path' => url('/design')]),
        ]);
    }

    public function admin(): View
    {
        return view('design.admin');
    }
}
