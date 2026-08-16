<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\QuoteBasketService;
use Illuminate\Routing\Controllers\HasMiddleware;

class CustomerDashboardController extends Controller implements HasMiddleware
{
    protected QuoteBasketService $quoteBasket;

    public static function middleware(): array
    {
        return ['auth', 'b2b.access'];
    }

    public function __construct(QuoteBasketService $quoteBasket)
    {
        $this->quoteBasket = $quoteBasket;
    }

    /**
     * Show the logged-in customer's account dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Recent orders
        $recentOrders = $user->orders()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Recent quotes
        $recentQuotes = $user->quotes()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Order stats
        $orderStats = $user->getOrderStatusCounts();
        $totalSpent = (float) $user->orders()
            ->where('status', 'approved')
            ->sum('grand_total');

        // Default address
        $defaultAddress = $user->addresses()
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => ($orderStats['submitted'] ?? 0) + ($orderStats['processing'] ?? 0),
            'total_spent' => $totalSpent,
            'quote_count' => $this->quoteBasket->count(),
            'saved_quotes' => $user->quotes()->count(),
        ];

        return view('customer.dashboard', compact('user', 'recentOrders', 'recentQuotes', 'stats', 'defaultAddress'));
    }
}
