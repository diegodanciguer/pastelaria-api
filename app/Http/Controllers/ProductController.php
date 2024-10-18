<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Lists all products.
     */
    public function list()
    {
        $products = Product::all();
        return response()->json($products, 200);
    }

    /**
     * Creates a new product.
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $validatedData['image'] = $path;
        }

        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    /**
     * Displays details of a specific product.
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * Updates a specific product.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Deletes the old image if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('images', 'public');
            $validatedData['image'] = $path;
        }

        $product->update($validatedData);

        return response()->json($product, 200);
    }

    /**
     * Deletes a specific product (Soft Delete).
     */
    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }

    /**
     * Restores a deleted product.
     */
    public function restore($id)
    {
        $product = Product::withTrashed()->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        if (!$product->trashed()) {
            return response()->json(['message' => 'Product is not deleted.'], 400);
        }

        $product->restore();

        return response()->json(['message' => 'Product restored successfully.'], 200);
    }
}
