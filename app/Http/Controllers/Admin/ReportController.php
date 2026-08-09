<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Sales report
     */
    public function sales()
    {
        $orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $revenue = Order::whereIn('status', ['approved', 'shipped', 'delivered'])->sum('grand_total');
        $monthlyRevenue = Order::whereIn('status', ['approved', 'shipped', 'delivered'])
            ->whereMonth('created_at', now()->month)
            ->sum('grand_total');

        $statusCounts = [
            'submitted' => Order::where('status', 'submitted')->count(),
            'approved' => Order::where('status', 'approved')->count(),
            'rejected' => Order::where('status', 'rejected')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];

        return view('admin.reports.sales', compact('orders', 'revenue', 'monthlyRevenue', 'statusCounts'));
    }

    /**
     * Inventory report
     */
    public function inventory()
    {
        $products = Product::with('variants')
            ->orderBy('name')
            ->paginate(25);

        $lowStock = Product::whereHas('variants', function ($q) {
            $q->whereColumn('stock_quantity', '<=', 'reorder_level');
        })->count();

        $outOfStock = Product::whereHas('variants', function ($q) {
            $q->where('stock_quantity', '=', 0);
        })->count();

        return view('admin.reports.inventory', compact('products', 'lowStock', 'outOfStock'));
    }

    /**
     * Customer report
     */
    public function customers()
    {
        $customers = User::withCount('orders')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.reports.customers', compact('customers'));
    }
}
