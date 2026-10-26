<?php

namespace App\Http\Controllers;

use App\Services\DashboardStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request, DashboardStatsService $stats): View
    {
        $range = $request->string('range', '30d')->toString();

        return view('admin.dashboard', [
            'summary' => $stats->summary(),
            'revenue' => $stats->revenueSeries($range),
            'revenueRange' => $range,
            'ordersByStatus' => $stats->ordersByStatus(),
            'topProducts' => $stats->topProducts(),
            'lowStockProducts' => $stats->lowStockProducts(),
            'recentOrders' => $stats->recentOrders(),
        ]);
    }
}
