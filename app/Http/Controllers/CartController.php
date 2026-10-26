<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(Request $request): View
    {
        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        $cart->load('items.product.primaryImage');

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer'], 'quantity' => ['nullable', 'integer', 'min:1', 'max:99']]);
        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        try {
            $this->cartService->add($cart, (int) $data['product_id'], (int) ($data['quantity'] ?? 1));
        } catch (DomainException $exception) {
            return back()->withErrors(['cart' => $exception->getMessage()]);
        }

        return back()->with('success', __('ui.cart.added'));
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        $this->cartService->update($cart, $product, (int) $data['quantity']);

        return back()->with('success', __('ui.cart.updated'));
    }

    public function destroy(Request $request, int $product): RedirectResponse
    {
        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        $this->cartService->remove($cart, $product);

        return back()->with('success', __('ui.cart.removed'));
    }

    public function clear(Request $request): RedirectResponse
    {
        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        $this->cartService->clear($cart);

        return back()->with('success', __('ui.cart.cleared'));
    }
}
