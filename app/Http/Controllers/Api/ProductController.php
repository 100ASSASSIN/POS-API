<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * List products (Admin, Manager, Cashier)
     */
    public function index()
    {
        $products = Product::with('category:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Create product (Admin, Manager only)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($data);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    /**
     * Update product (Admin, Manager only)
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    /**
     * Delete product (Admin only)
     */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
