<?php

namespace App\Http\Controllers;

use App\Actions\PlaceOrderAction;
use App\Contracts\PaymentGateway;
use App\Enums\PaymentMethod;
use App\Mail\OrderConfirmation;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly PlaceOrderAction $placeOrder,
    ) {}

    public function create(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        $cart->load('items.product');
        if ($cart->items->isEmpty()) {
            return Redirect::route('cart.index')->withErrors(['cart' => __('ui.cart.empty')]);
        }

        return view('checkout.index', [
            'cart' => $cart,
            'addresses' => $request->user()->addresses()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
            'payment_method' => ['required', 'in:cod,vnpay'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'loyalty_points' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $order = $this->placeOrder->execute($request->user(), $data);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['checkout' => $exception->getMessage()])->withInput();
        }

        Mail::to($request->user()->email)->queue(new OrderConfirmation($order->load('items')));

        if ($order->payment_method === PaymentMethod::Vnpay) {
            return Redirect::to(app(PaymentGateway::class)->createPaymentUrl($order));
        }

        return Redirect::route('orders.show', $order)->with('success', __('ui.orders.placed'));
    }
}
