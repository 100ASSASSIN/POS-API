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
    // Validate request
    $data = $request->validate([
        'category_id'   => 'required|exists:categories,id',
        'name'          => 'required|string|max:255',
        'price'         => 'required|numeric|min:0',
        'stock'         => 'required|integer|min:0',
        'sku'           => 'required|string|max:255|unique:products,sku',
        'status'        => 'required|in:active,inactive',
        'tax'         => 'nullable|numeric|min:0',
        'location'    => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,avif|max:5048',
    ]);

    // Convert status string to boolean
    $data['status'] = $data['status'] === 'active' ? 1 : 0;

    // Handle image upload
     // If a new image is uploaded, convert to Base64
    if ($request->hasFile('product_image')) {
        $imageFile = $request->file('product_image');
        $imageContents = file_get_contents($imageFile->getRealPath());
        $base64Image = 'data:' . $imageFile->getMimeType() . ';base64,' . base64_encode($imageContents);
        $data['product_image'] = $base64Image;
    }

    // Create product
    $product = Product::create($data);

    return response()->json([
        'message' => 'Product created successfully',
        'product' => $product,
    ], 201);
}


    /**
     * Update product (Admin, Manager only)
     */
public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // Validate request
    $data = $request->validate([
        'category_id'   => 'required|exists:categories,id',
        'name'          => 'required|string|max:255',
        'price'         => 'required|numeric|min:0',
        'stock'         => 'required|integer|min:0',
        'sku'           => 'required|string|max:255|unique:products,sku,' . $id,
        'status'        => 'required|in:active,inactive',
        'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,avif|max:5048',
        'tax'         => 'nullable|numeric|min:0',
        'description' => 'nullable|string',
        'location'    => 'nullable|string|max:255',
    ]);

    // If a new image is uploaded, convert to Base64
    if ($request->hasFile('product_image')) {
        $imageFile = $request->file('product_image');
        $imageContents = file_get_contents($imageFile->getRealPath());
        $base64Image = 'data:' . $imageFile->getMimeType() . ';base64,' . base64_encode($imageContents);
        $data['product_image'] = $base64Image;
    }

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
