<?php

namespace App\Livewire;

use App\Enums\LifeStage;
use App\Enums\ProductSpecies;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\RecommendationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $species = '';

    #[Url(as: 'stage', except: '')]
    public string $lifeStage = '';

    #[Url(as: 'category', except: '')]
    public string $categoryId = '';

    #[Url(as: 'brand', except: '')]
    public string $brandId = '';

    #[Url(as: 'min_price', except: '')]
    public string $minPrice = '';

    #[Url(as: 'max_price', except: '')]
    public string $maxPrice = '';

    #[Url(as: 'sale', except: false)]
    public bool $onSale = false;

    #[Url(as: 'in_stock', except: true)]
    public bool $inStock = true;

    #[Url(as: 'for_pet', except: false)]
    public bool $forPet = false;

    #[Url(as: 'safe_only', except: false)]
    public bool $safeOnly = false;

    #[Url(except: 'newest')]
    public string $sort = 'newest';

    public function mount(string|int|null $categoryId = null, string|int|null $brandId = null): void
    {
        if ($categoryId !== null && $this->categoryId === '') {
            $this->categoryId = (string) $categoryId;
        }

        if ($brandId !== null && $this->brandId === '') {
            $this->brandId = (string) $brandId;
        }
    }

    public function updated($property): void
    {
        if ($property !== 'page') {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'species', 'lifeStage', 'categoryId', 'brandId', 'minPrice', 'maxPrice', 'onSale']);
        $this->inStock = true;
        $this->sort = 'newest';
        $this->resetPage();
    }

    public function render(): View
    {
        $currentPet = auth()->user()?->isCustomer()
            ? auth()->user()->pets()->find(session('current_pet_id'))
            : null;
        $recommendations = $currentPet ? app(RecommendationService::class) : null;
        $products = Product::query()
            ->active()
            ->when($this->search !== '', function ($query): void {
                $term = '%'.$this->search.'%';
                $query->where(function ($query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', $term));
                });
            })
            ->when($this->species !== '', fn ($query) => $query->where('species', $this->species))
            ->when($this->lifeStage !== '', fn ($query) => $query->where('life_stage', $this->lifeStage))
            ->when($this->categoryId !== '', fn ($query) => $query->where('category_id', $this->categoryId))
            ->when($this->brandId !== '', fn ($query) => $query->where('brand_id', $this->brandId))
            ->when($this->minPrice !== '' && is_numeric($this->minPrice), fn ($query) => $query->whereRaw('COALESCE(sale_price, price) >= ?', [(int) $this->minPrice]))
            ->when($this->maxPrice !== '' && is_numeric($this->maxPrice), fn ($query) => $query->whereRaw('COALESCE(sale_price, price) <= ?', [(int) $this->maxPrice]))
            ->when($this->onSale, fn ($query) => $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price'))
            ->when($this->inStock, fn ($query) => $query->inStock())
            ->when($this->forPet && $currentPet, function ($query) use ($currentPet, $recommendations): void {
                $query->where(function ($query) use ($currentPet): void {
                    $query->where('species', ProductSpecies::All->value)->orWhere('species', $currentPet->species->value);
                });
                if ($this->safeOnly) {
                    $safeIds = $recommendations->recommended($currentPet, 100, true)->modelKeys();
                    $query->whereKey($safeIds);
                }
            })
            ->with(['primaryImage', 'brand'])
            ->withRatingSummary();

        match ($this->sort) {
            'price_asc' => $products->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $products->orderByRaw('COALESCE(sale_price, price) desc'),
            'best_selling' => $products->withSum('orderItems as sold_quantity', 'quantity')->orderByDesc('sold_quantity'),
            default => $products->latest('id'),
        };

        return view('livewire.product-catalog', [
            'products' => $products->paginate(12),
            'categories' => Category::query()->active()->roots()->with('children')->orderBy('sort_order')->get(),
            'brands' => Brand::query()->active()->orderBy('name')->get(),
            'speciesOptions' => ProductSpecies::cases(),
            'lifeStageOptions' => LifeStage::cases(),
            'currentPet' => $currentPet,
        ]);
    }
}
