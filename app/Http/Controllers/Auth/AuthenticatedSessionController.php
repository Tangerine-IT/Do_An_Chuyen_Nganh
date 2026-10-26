<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, CartService $cartService): RedirectResponse
    {
        $guestSessionId = $request->session()->getId();
        $request->authenticate();

        $request->session()->regenerate();
        $cartService->mergeGuestCart($request->user(), $guestSessionId);

        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($request->user()->isStaff()) {
            return redirect()->route('admin.bookings.index');
        }

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
