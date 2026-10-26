<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\LoyaltyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->string('q')->trim()->value(), fn ($query, $term) => $query->where('order_number', 'like', "%{$term}%"))
            ->latest('placed_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load(['user', 'items'])]);
    }

    public function updateStatus(Request $request, Order $order, LoyaltyService $loyalty): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:pending,confirmed,shipping,completed,cancelled']]);
        $from = $order->status;
        $to = OrderStatus::from($data['status']);
        $allowed = [
            OrderStatus::Pending->value => [OrderStatus::Confirmed, OrderStatus::Cancelled],
            OrderStatus::Confirmed->value => [OrderStatus::Shipping, OrderStatus::Cancelled],
            OrderStatus::Shipping->value => [OrderStatus::Completed],
            OrderStatus::Completed->value => [],
            OrderStatus::Cancelled->value => [],
        ];
        if (! in_array($to, $allowed[$from->value], true)) {
            throw new RuntimeException(__('ui.admin.invalid_transition'));
        }

        DB::transaction(function () use ($order, $to): void {
            $order->update(['status' => $to]);
            if ($to === OrderStatus::Cancelled) {
                foreach ($order->items()->whereNotNull('product_id')->get() as $item) {
                    $item->product()->lockForUpdate()->increment('stock', $item->quantity);
                }
            }
        });
        if ($to === OrderStatus::Completed) {
            $loyalty->earnOrder($order->fresh('user'));
        }

        return back()->with('success', __('ui.admin.saved'));
    }

    public function markCodPaid(Order $order): RedirectResponse
    {
        abort_unless($order->payment_method === PaymentMethod::Cod, 422);
        abort_unless($order->payment_status !== PaymentStatus::Paid, 422);

        DB::transaction(function () use ($order): void {
            $order->update(['payment_status' => PaymentStatus::Paid]);
            $order->payments()->latest()->first()?->update([
                'status' => PaymentStatus::Paid,
                'paid_at' => now(),
            ]);
        });

        return back()->with('success', __('ui.admin.saved'));
    }
}
