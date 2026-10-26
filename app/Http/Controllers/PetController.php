<?php

namespace App\Http\Controllers;

use App\Enums\PetSex;
use App\Enums\PetSpecies;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PetController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.pets', [
            'pets' => $request->user()->pets()->latest()->get(),
            'currentPet' => $request->user()->pets()->find(session('current_pet_id')),
            'species' => collect(PetSpecies::cases())->mapWithKeys(fn (PetSpecies $species): array => [$species->value => $species->label()]),
            'sexes' => collect(PetSex::cases())->mapWithKeys(fn (PetSex $sex): array => [$sex->value => $sex->label()]),
            'breeds' => [
                'dog' => ['Poodle', 'Corgi', 'Golden Retriever', 'Pug', 'Phốc sóc', 'Labrador'],
                'cat' => ['Mèo Anh lông ngắn', 'Mèo ta', 'Mèo Ba Tư', 'Mèo Munchkin', 'Mèo Bengal'],
                'bird' => ['Vẹt', 'Yến phụng', 'Chim finch'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'species' => ['required', 'in:'.implode(',', array_column(PetSpecies::cases(), 'value'))],
            'breed' => ['nullable', 'string', 'max:100'],
            'birthday' => ['nullable', 'date', 'before_or_equal:today'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'sex' => ['required', 'in:'.implode(',', array_column(PetSex::cases(), 'value'))],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['allergies'] = filled($data['allergies'] ?? null)
            ? array_values(array_filter(array_map('trim', explode(',', $data['allergies']))))
            : null;
        $data['public_token'] = Str::random(48);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('pets', 'public');
        }
        unset($data['photo']);

        $request->user()->pets()->create($data);

        return back()->with('success', __('ui.account.pet_saved'));
    }

    public function setCurrent(Request $request, Pet $pet): RedirectResponse
    {
        Gate::authorize('view', $pet);
        session(['current_pet_id' => $pet->id]);

        return back()->with('success', __('ui.account.current_pet_saved', ['name' => $pet->name]));
    }

    public function clearCurrent(Request $request): RedirectResponse
    {
        $request->session()->forget('current_pet_id');

        return back();
    }

    public function destroy(Pet $pet): RedirectResponse
    {
        Gate::authorize('delete', $pet);
        if ($pet->photo_path) {
            Storage::disk('public')->delete($pet->photo_path);
        }
        if ((int) session('current_pet_id') === $pet->id) {
            session()->forget('current_pet_id');
        }
        $pet->delete();

        return back()->with('success', __('ui.account.pet_deleted'));
    }
}
