<?php

namespace App\Http\Controllers;

use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(Request $request, LoyaltyService $loyalty): View
    {
        $user = $request->user();

        return view('account.loyalty', [
            'transactions' => $user->loyaltyTransactions()->latest('created_at')->paginate(20),
            'tier' => $loyalty->tier($user),
            'perks' => config('loyalty.tiers.'.$loyalty->tier($user).'.perks'),
            'badges' => $loyalty->badges($user),
        ]);
    }
}
