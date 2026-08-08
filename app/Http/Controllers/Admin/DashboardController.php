<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Admin;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $admin = auth()->guard('admin')->user();
        
        // Cache stats for 5 minutes
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                // Orders
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'submitted')->count(),
                'processing_orders' => Order::where('status', 'processing')->count(),
                'approved_orders' => Order::where('status', 'approved')->count(),
                'shipped_orders' => Order::where('status', 'shipped')->count(),
                'delivered_orders' => Order::where('status', 'delivered')->count(),
                'cancelled_orders' => Order::where('status', 'cancelled')->count(),
                'rejected_orders' => Order::where('status', 'rejected')->count(),
                
                // Revenue
                'total_revenue' => Order::whereIn('status', ['approved', 'shipped', 'delivered'])->sum('grand_total'),
                'monthly_revenue' => Order::whereIn('status', ['approved', 'shipped', 'delivered'])
                    ->whereMonth('created_at', now()->month)
                    ->sum('grand_total'),
                
                // Customers
                'total_customers' => User::count(),
                'pending_approvals' => User::where('is_approved', false)->count(),
                'active_customers' => User::where('is_approved', true)->count(),
                'new_customers_this_month' => User::whereMonth('created_at', now()->month)->count(),
                
                // Products
                'total_products' => Product::where('is_active', true)->count(),
                'inactive_products' => Product::where('is_active', false)->count(),
                'low_stock_products' => Product::whereHas('variants', function($q) {
                    $q->whereColumn('stock_quantity', '<=', 'reorder_level');
                })->count(),
                'out_of_stock_products' => Product::whereHas('variants', function($q) {
                    $q->where('stock_quantity', '=', 0);
                })->count(),
                
                // Quotes
                'total_quotes' => Quote::count(),
                'pending_quotes' => Quote::where('status', 'submitted')->count(),
            ];
        });

        // Recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent customers
        $recentCustomers = User::where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Pending approvals
        $pendingCustomers = User::where('is_approved', false)
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // Monthly sales chart data
        $monthlyData = $this->getMonthlySalesData();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'recentCustomers',
            'pendingCustomers',
            'monthlyData',
            'admin'
        ));
    }

    private function getMonthlySalesData()
    {
        $months = [];
        $sales = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $sales[] = Order::whereIn('status', ['approved', 'shipped', 'delivered'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('grand_total');
        }

        return [
            'months' => $months,
            'sales' => $sales,
        ];
    }
}