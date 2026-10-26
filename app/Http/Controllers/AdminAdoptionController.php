<?php

namespace App\Http\Controllers;

use App\Enums\AdoptionApplicationStatus;
use App\Enums\AdoptionStatus;
use App\Models\AdoptionApplication;
use App\Models\AdoptionListing;
use App\Notifications\AdoptionApplicationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAdoptionController extends Controller
{
    public function listings(): View
    {
        return view('admin.adoption.listings', ['listings' => AdoptionListing::query()->with('user')->latest()->paginate(15)]);
    }

    public function updateListing(Request $request, AdoptionListing $listing): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:available,pending,adopted']]);
        $listing->status = AdoptionStatus::from($data['status']);
        $listing->save();

        return back()->with('success', __('ui.admin.saved'));
    }

    public function applications(): View
    {
        return view('admin.adoption.applications', [
            'applications' => AdoptionApplication::query()->with(['listing', 'user'])->latest()->paginate(15),
        ]);
    }

    public function updateApplication(Request $request, AdoptionApplication $application): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,withdrawn'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $application->status = AdoptionApplicationStatus::from($data['status']);
        $application->admin_note = $data['admin_note'] ?? null;
        $application->save();
        $application->user->notify(new AdoptionApplicationNotification($application, 'updated'));
        if ($application->status === AdoptionApplicationStatus::Approved) {
            $application->listing->status = AdoptionStatus::Adopted;
            $application->listing->save();
        }

        return back()->with('success', __('ui.admin.saved'));
    }
}
