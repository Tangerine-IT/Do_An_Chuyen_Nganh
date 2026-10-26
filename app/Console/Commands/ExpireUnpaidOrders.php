<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid';

    protected $description = 'Cancel expired unpaid online orders and restore stock';

    public function handle(): int
    {
        $count = 0;
        Order::query()->where('payment_method', '!=', PaymentMethod::Cod)
            ->where('payment_status', PaymentStatus::Pending)
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<', now())
            ->with('items')
            ->each(function (Order $order) use (&$count): void {
                DB::transaction(function () use ($order, &$count): void {
                    $locked = Order::query()->whereKey($order->id)->lockForUpdate()->with('items')->first();
                    if (! $locked || $locked->payment_status !== PaymentStatus::Pending) {
                        return;
                    }
                    foreach ($locked->items as $item) {
                        $item->product()->lockForUpdate()->increment('stock', $item->quantity);
                    }
                    $locked->update(['status' => OrderStatus::Cancelled, 'payment_status' => PaymentStatus::Failed]);
                    $count++;
                });
            });
        $this->info("Expired {$count} order(s).");

        return self::SUCCESS;
    }
}
