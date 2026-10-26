<?php

namespace App\Http\Controllers;

use App\Enums\AdoptionApplicationStatus;
use App\Enums\AdoptionStatus;
use App\Models\AdoptionListing;
use App\Notifications\AdoptionApplicationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdoptionController extends Controller
{
    public function index(Request $request): View
    {
        return view('adoption.index', [
            'listings' => AdoptionListing::query()->available()
                ->when($request->filled('species'), fn ($query) => $query->where('species', $request->string('species')))
                ->when($request->filled('city'), fn ($query) => $query->where('city', 'like', '%'.$request->string('city').'%'))
                ->when($request->filled('age'), function ($query) use ($request): void {
                    if ($request->string('age')->value() === 'young') {
                        $query->where('age_months', '<', 12);
                    } elseif ($request->string('age')->value() === 'adult') {
                        $query->whereBetween('age_months', [12, 84]);
                    } elseif ($request->string('age')->value() === 'senior') {
                        $query->where('age_months', '>', 84);
                    }
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function show(AdoptionListing $listing): View
    {
        return view('adoption.show', compact('listing'));
    }

    public function apply(Request $request, AdoptionListing $listing): RedirectResponse
    {
        abort_unless($listing->status === AdoptionStatus::Available, 422);
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:2000'],
        ]);
        $existing = $listing->applications()
            ->where('user_id', $request->user()->id)
            ->where('status', AdoptionApplicationStatus::Pending)
            ->exists();
        if ($existing) {
            return back()->withErrors(['application' => __('ui.adoption.already_applied')]);
        }

        $application = $listing->applications()->create([
            ...$data,
            'user_id' => $request->user()->id,
            'status' => AdoptionApplicationStatus::Pending,
        ]);
        $request->user()->notify(new AdoptionApplicationNotification($application, 'submitted'));

        return back()->with('success', __('ui.adoption.application_sent'));
    }
}
