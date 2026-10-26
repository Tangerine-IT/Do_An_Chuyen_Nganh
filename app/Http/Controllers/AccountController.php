<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\RecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request, RecommendationService $recommendations): View
    {
        $currentPet = $request->user()->pets()->find(session('current_pet_id'));

        return view('account.index', [
            'user' => $request->user(),
            'currentPet' => $currentPet,
            'recommendedProducts' => $currentPet ? $recommendations->recommended($currentPet, 4) : collect(),
            'upcomingHealthRecords' => $currentPet ? $currentPet->healthDueRecords()->limit(5)->get() : collect(),
        ]);
    }

    public function addresses(Request $request): View
    {
        return view('account.addresses', ['addresses' => $request->user()->addresses()->latest()->get()]);
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:255'],
            'ward' => ['nullable', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'province' => ['required', 'string', 'max:120'],
            'is_default' => ['boolean'],
        ]);

        $user = $request->user();
        if (($data['is_default'] ?? false) || ! $user->addresses()->exists()) {
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }
        $user->addresses()->create($data);

        return back()->with('success', __('ui.account.address_saved'));
    }

    public function destroyAddress(Request $request, Address $address): RedirectResponse
    {
        Gate::authorize('delete', $address);
        $wasDefault = $address->is_default;
        $address->delete();
        if ($wasDefault) {
            $request->user()->addresses()->latest('id')->first()?->update(['is_default' => true]);
        }

        return back()->with('success', __('ui.account.address_deleted'));
    }
}
