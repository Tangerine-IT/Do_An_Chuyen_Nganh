<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLocationController extends Controller
{
    public function index(Request $request): View
    {
        $locations = Location::query()
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.locations.form', ['location' => new Location(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Location::create($this->validated($request));

        return redirect()->route('admin.locations.index')->with('success', __('ui.admin.saved'));
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.form', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $location->update($this->validated($request));

        return redirect()->route('admin.locations.index')->with('success', __('ui.admin.saved'));
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return back()->with('success', __('ui.admin.deleted'));
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:shop,vet,pet_friendly'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
