<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        return Cache::remember('admin.dashboard.summary', now()->addSeconds(60), function (): array {
            $today = now()->startOfDay();
            $monthStart = now()->startOfMonth();
            $lastMonthStart = now()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->startOfMonth();
            $revenueStatuses = fn ($query) => $query->where('status', '!=', OrderStatus::Cancelled->value);

            $todayRevenue = Order::query()->where($revenueStatuses)->where('placed_at', '>=', $today)->sum('total');
            $monthRevenue = Order::query()->where($revenueStatuses)->where('placed_at', '>=', $monthStart)->sum('total');
            $lastMonthRevenue = Order::query()->where($revenueStatuses)->whereBetween('placed_at', [$lastMonthStart, $lastMonthEnd])->sum('total');

            return [
                'todayRevenue' => (int) $todayRevenue,
                'monthRevenue' => (int) $monthRevenue,
                'lastMonthRevenue' => (int) $lastMonthRevenue,
                'monthRevenueChange' => $this->percentageChange((int) $lastMonthRevenue, (int) $monthRevenue),
                'newOrders' => Order::query()->where('placed_at', '>=', $today)->count(),
                'pendingOrders' => Order::query()->where('status', OrderStatus::Pending)->count(),
                'newCustomers' => User::query()->where('created_at', '>=', $monthStart)->where('role', 'customer')->count(),
                'upcomingBookings' => Booking::query()
                    ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
                    ->whereBetween('starts_at', [now(), now()->addDays(7)->endOfDay()])
                    ->count(),
            ];
        });
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function revenueSeries(string $range): array
    {
        $range = in_array($range, ['7d', '30d', '12m'], true) ? $range : '30d';
        $cacheKey = 'admin.dashboard.revenue.'.$range;

        return Cache::remember($cacheKey, now()->addSeconds(60), function () use ($range): array {
            $start = match ($range) {
                '7d' => now()->startOfDay()->subDays(6),
                '12m' => now()->startOfMonth()->subMonths(11),
                default => now()->startOfDay()->subDays(29),
            };
            $format = $range === '12m' ? '%Y-%m' : '%Y-%m-%d';
            $expression = DB::getDriverName() === 'sqlite'
                ? "strftime('{$format}', placed_at)"
                : ($range === '12m' ? "DATE_FORMAT(placed_at, '%Y-%m')" : "DATE_FORMAT(placed_at, '%Y-%m-%d')");
            $rows = Order::query()
                ->where('status', '!=', OrderStatus::Cancelled)
                ->where('placed_at', '>=', $start)
                ->selectRaw("{$expression} as bucket, SUM(total) as revenue")
                ->groupBy('bucket')
                ->pluck('revenue', 'bucket');

            $labels = [];
            $values = [];
            if ($range === '12m') {
                for ($date = $start->copy(); $date <= now()->startOfMonth(); $date->addMonth()) {
                    $key = $date->format('Y-m');
                    $labels[] = $date->format('m/Y');
                    $values[] = (int) ($rows[$key] ?? 0);
                }
            } else {
                $days = $range === '7d' ? 7 : 30;
                for ($date = $start->copy(); $date <= now()->startOfDay(); $date->addDay()) {
                    $key = $date->format('Y-m-d');
                    $labels[] = $date->format('d/m');
                    $values[] = (int) ($rows[$key] ?? 0);
                }
            }

            return compact('labels', 'values');
        });
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function ordersByStatus(): array
    {
        return Cache::remember('admin.dashboard.orders-status', now()->addSeconds(60), function (): array {
            $rows = Order::query()->select('status')->selectRaw('COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

            return [
                'labels' => array_map(fn (OrderStatus $status): string => $status->label(), OrderStatus::cases()),
                'values' => array_map(fn (OrderStatus $status): int => (int) ($rows[$status->value] ?? 0), OrderStatus::cases()),
            ];
        });
    }

    /**
     * @return list<array{name: string, quantity: int, revenue: int}>
     */
    public function topProducts(): array
    {
        return Cache::remember('admin.dashboard.top-products', now()->addSeconds(60), function (): array {
            return DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', '!=', OrderStatus::Cancelled->value)
                ->select('order_items.product_name as name')
                ->selectRaw('SUM(order_items.quantity) as quantity, SUM(order_items.unit_price * order_items.quantity) as revenue')
                ->groupBy('order_items.product_name')
                ->orderByDesc('quantity')
                ->limit(10)
                ->get()
                ->map(fn ($row): array => [
                    'name' => $row->name,
                    'quantity' => (int) $row->quantity,
                    'revenue' => (int) $row->revenue,
                ])
                ->all();
        });
    }

    /** @return Collection<int, Product> */
    public function lowStockProducts()
    {
        return Product::query()
            ->active()
            ->where('stock', '<=', config('petshop.low_stock_threshold'))
            ->orderBy('stock')
            ->limit(8)
            ->get();
    }

    /** @return Collection<int, Order> */
    public function recentOrders()
    {
        return Order::query()->with('user')->latest('placed_at')->limit(8)->get();
    }

    private function percentageChange(int $previous, int $current): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
