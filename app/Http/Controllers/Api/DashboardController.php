<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        // Today range
        $todayStart = now()->startOfDay();
        $todayEnd   = now()->endOfDay();

        return response()->json([
            'sales' => [
                'today_sales' => Order::whereBetween('created_at', [$todayStart, $todayEnd])
                    ->sum('final_amount'),

                'total_sales' => Order::sum('final_amount'),

                'today_orders' => Order::whereBetween('created_at', [$todayStart, $todayEnd])
                    ->count(),

                'total_orders' => Order::count(),
            ],

            'payments' => [
                'cash' => Order::where('payment_method', 'cash')->sum('final_amount'),
                'card' => Order::where('payment_method', 'card')->sum('final_amount'),
                'upi'  => Order::where('payment_method', 'upi')->sum('final_amount'),
            ],

            'products' => [
                'total_products' => Product::count(),
                'low_stock' => Product::where('stock', '<=', 5)->count(),
                'out_of_stock' => Product::where('stock', '<=', 0)->count(),
            ],

            'customers' => [
                'total_customers' => Customer::count(),
            ],

            'recent_orders' => Order::with('customer')
                ->latest()
                ->limit(5)
                ->get([
                    'id',
                    'customer_id',
                    'final_amount',
                    'payment_method',
                    'status',
                    'created_at'
                ]),
        ]);
    }
}
