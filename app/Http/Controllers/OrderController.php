<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\Product;
use App\Mail\OrderCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Lists all orders.
     */
    public function list()
    {
        $orders = Order::with(['client', 'products'])->get();
        return response()->json($orders, 200);
    }

    /**
     * Creates a new order.
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'client_id' => $validatedData['client_id'],
        ]);

        foreach ($validatedData['products'] as $product) {
            $order->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        $order->load('products', 'client');

        Mail::to($order->client->email)->send(new OrderCreatedMail($order));

        return response()->json($order->load('products'), 201);
    }

    /**
     * Displays details of a specific order.
     */
    public function show($id)
    {
        $order = Order::with(['client', 'products'])->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json($order, 200);
    }

    /**
     * Updates a specific order.
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $validatedData = $request->validate([
            'client_id' => 'sometimes|required|exists:clients,id',
            'products' => 'sometimes|required|array|min:1',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
        ]);

        if (isset($validatedData['client_id'])) {
            $order->client_id = $validatedData['client_id'];
            $order->save();
        }

        if (isset($validatedData['products'])) {
            $syncData = [];
            foreach ($validatedData['products'] as $product) {
                $syncData[$product['id']] = ['quantity' => $product['quantity']];
            }
            $order->products()->sync($syncData);
        }

        $order->load('products', 'client');

        return response()->json($order, 200);
    }

    /**
     * Deletes a specific order (Soft Delete).
     */
    public function delete($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully.'], 200);
    }

    /**
     * Restores a deleted order.
     */
    public function restore($id)
    {
        $order = Order::withTrashed()->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if (!$order->trashed()) {
            return response()->json(['message' => 'Order is not deleted.'], 400);
        }

        $order->restore();

        return response()->json(['message' => 'Order restored successfully.'], 200);
    }
}
