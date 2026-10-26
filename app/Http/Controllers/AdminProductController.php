<?php

namespace App\Http\Controllers;

use App\Enums\LifeStage;
use App\Enums\ProductSpecies;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'brand'])
            ->when($request->string('q')->trim()->value(), fn ($query, $term) => $query->where(fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%")))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->boolean('status')))
            ->orderBy($request->input('sort', 'name'), $request->input('direction', 'asc'))
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.form', $this->formData(new Product(['is_active' => true])));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->safe()->except('images'));
        $this->storeImages($product, $request);

        return redirect()->route('admin.products.index')->with('success', __('ui.admin.saved'));
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', $this->formData($product));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->safe()->except('images'));
        $this->storeImages($product, $request);

        return redirect()->route('admin.products.index')->with('success', __('ui.admin.saved'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $product->delete();

        return back()->with('success', __('ui.admin.deleted'));
    }

    public function destroyImage(Product $product, int $image): RedirectResponse
    {
        $productImage = $product->images()->findOrFail($image);
        Storage::disk('public')->delete($productImage->path);
        $productImage->delete();

        return back()->with('success', __('ui.admin.deleted'));
    }

    public function setPrimaryImage(Product $product, int $image): RedirectResponse
    {
        $product->images()->update(['is_primary' => false]);
        $product->images()->whereKey($image)->update(['is_primary' => true]);

        return back()->with('success', __('ui.admin.image_primary'));
    }

    /** @return array<string, mixed> */
    private function formData(Product $product): array
    {
        return [
            'product' => $product->load('images'),
            'categories' => Category::query()->orderBy('name')->get(),
            'brands' => Brand::query()->orderBy('name')->get(),
            'species' => ProductSpecies::cases(),
            'lifeStages' => LifeStage::cases(),
        ];
    }

    private function storeImages(Product $product, Request $request): void
    {
        foreach ($request->file('images', []) as $index => $file) {
            $product->images()->create([
                'path' => $file->store('products', 'public'),
                'alt_text' => $product->name,
                'sort_order' => (int) $product->images()->max('sort_order') + $index + 1,
                'is_primary' => ! $product->images()->exists() && $index === 0,
            ]);
        }
    }
}
