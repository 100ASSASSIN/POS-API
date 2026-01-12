<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,upi',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        return DB::transaction(function () use ($request) {

            // Create / Find Customer
            $customerId = null;
            if ($request->customer_name) {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                ]);
                $customerId = $customer->id;
            }

            // Create Order
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_id' => $customerId,
                'total_amount' => $request->subtotal,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'final_amount' => $request->total,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);

            // Order Items + Stock Update
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                Product::where('id', $item['product_id'])
                    ->decrement('stock', $item['quantity']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $order->id,
            ], 201);
        });
    }
}
