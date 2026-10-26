<?php

namespace App\Http\Controllers;

use App\Actions\PlaceOrderAction;
use App\Contracts\PaymentGateway;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.orders', [
            'orders' => $request->user()->orders()->latest('placed_at')->paginate(10),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        Gate::authorize('view', $order);
        $order->load('items');

        return view('account.orders-show', compact('order'));
    }

    public function payAgain(Request $request, Order $order, PaymentGateway $gateway): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->payment_method === PaymentMethod::Vnpay, 422);
        abort_unless($order->payment_status !== PaymentStatus::Paid, 422);
        $order->update(['payment_status' => PaymentStatus::Pending, 'payment_expires_at' => now()->addMinutes(15)]);
        $order->payments()->create(['method' => PaymentMethod::Vnpay, 'status' => PaymentStatus::Pending, 'amount' => $order->total]);

        return redirect()->to($gateway->createPaymentUrl($order));
    }

    public function cancel(Request $request, Order $order, PlaceOrderAction $placeOrder): RedirectResponse
    {
        Gate::authorize('view', $order);
        try {
            $placeOrder->cancel($request->user(), $order);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['order' => $exception->getMessage()]);
        }

        return back()->with('success', __('ui.orders.cancelled'));
    }
}
