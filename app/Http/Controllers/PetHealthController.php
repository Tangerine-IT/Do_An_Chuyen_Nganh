<?php

namespace App\Http\Controllers;

use App\Enums\HealthRecordType;
use App\Models\Pet;
use App\Models\PetHealthRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PetHealthController extends Controller
{
    public function show(Pet $pet): View
    {
        Gate::authorize('view', $pet);
        if (! $pet->public_token) {
            $pet->update(['public_token' => Str::random(48)]);
        }

        return view('account.pet-health', [
            'pet' => $pet->load(['healthRecords' => fn ($query) => $query->latest('record_date')]),
            'types' => collect(HealthRecordType::cases())->mapWithKeys(fn (HealthRecordType $type): array => [$type->value => $type->label()]),
        ]);
    }

    public function store(Request $request, Pet $pet): RedirectResponse
    {
        Gate::authorize('update', $pet);
        $data = $this->validated($request);
        $data['attachment_path'] = $request->hasFile('attachment')
            ? $request->file('attachment')->store('pet-health', 'public')
            : null;

        unset($data['attachment']);
        $pet->healthRecords()->create($data);

        return back()->with('success', __('ui.account.health_saved'));
    }

    public function update(Request $request, Pet $pet, PetHealthRecord $record): RedirectResponse
    {
        Gate::authorize('update', $pet);
        abort_unless($record->pet_id === $pet->id, 404);
        $data = $this->validated($request);

        if ($request->hasFile('attachment')) {
            if ($record->attachment_path) {
                Storage::disk('public')->delete($record->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('pet-health', 'public');
        }
        unset($data['attachment']);
        $record->update($data + ['reminded_at' => null]);

        return back()->with('success', __('ui.account.health_updated'));
    }

    public function destroy(Pet $pet, PetHealthRecord $record): RedirectResponse
    {
        Gate::authorize('update', $pet);
        abort_unless($record->pet_id === $pet->id, 404);
        if ($record->attachment_path) {
            Storage::disk('public')->delete($record->attachment_path);
        }
        $record->delete();

        return back()->with('success', __('ui.account.health_deleted'));
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:'.implode(',', array_column(HealthRecordType::cases(), 'value'))],
            'record_date' => ['required', 'date', 'before_or_equal:today'],
            'next_due_date' => ['nullable', 'date'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'note' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'image', 'max:5120'],
        ]);
    }
}
