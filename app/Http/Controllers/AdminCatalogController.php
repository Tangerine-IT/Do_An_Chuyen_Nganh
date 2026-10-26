<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCatalogController extends Controller
{
    public function index(string $resource): View
    {
        $model = $this->model($resource);
        $items = $model::query()
            ->when(request('q'), fn ($query, $term) => $query->where('name', 'like', '%'.$term.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.catalog.index', compact('items', 'resource'));
    }

    public function create(string $resource): View
    {
        return view('admin.catalog.form', ['resource' => $resource, 'item' => $this->model($resource)::make(['is_active' => true])]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $data = $this->validated($request, $resource);
        $this->model($resource)::create($data);

        return redirect()->route('admin.catalog.index', $resource)->with('success', __('ui.admin.saved'));
    }

    public function edit(string $resource, int $id): View
    {
        return view('admin.catalog.form', ['resource' => $resource, 'item' => $this->model($resource)::findOrFail($id)]);
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $item = $this->model($resource)::findOrFail($id);
        $item->update($this->validated($request, $resource));

        return redirect()->route('admin.catalog.index', $resource)->with('success', __('ui.admin.saved'));
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $this->model($resource)::findOrFail($id)->delete();

        return back()->with('success', __('ui.admin.deleted'));
    }

    /** @return class-string<Category|Brand|Service> */
    private function model(string $resource): string
    {
        return match ($resource) {
            'categories' => Category::class,
            'brands' => Brand::class,
            'services' => Service::class,
            default => abort(404),
        };
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, string $resource): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
        if ($resource === 'services') {
            $rules['duration_minutes'] = ['required', 'integer', 'min:1'];
            $rules['price'] = ['required', 'integer', 'min:0'];
        }
        if ($resource === 'categories') {
            $rules['sort_order'] = ['nullable', 'integer', 'min:0'];
        }

        $data = $request->validate($rules);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
