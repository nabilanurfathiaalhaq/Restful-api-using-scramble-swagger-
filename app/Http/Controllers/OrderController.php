<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'customer_id' => 'required|exists:customers,id',
        ]);

        $order = Order::create($validated);
        return response()->json($order, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|numeric|min:0',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($id);
        $order->update($validated);
        return response()->json($order);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }

    public function index()
    {
    return Order::with(['customer', 'product'])->get();
    }

    public function store(Request $request)
    {
    $validated = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $product = Product::findOrFail($validated['product_id']);
    $total_price = $product->price * $validated['quantity'];

    $order = Order::create([
        'customer_id' => $validated['customer_id'],
        'product_id' => $validated['product_id'],
        'quantity' => $validated['quantity'],
        'total_price' => $total_price,
    ]);

    return response()->json($order, 201);
    }
    
    public function update(Request $request, $id)
    {
    $validated = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $order = Order::findOrFail($id);
    $product = Product::findOrFail($validated['product_id']);
    $total_price = $product->price * $validated['quantity'];

    $order->update([
        'customer_id' => $validated['customer_id'],
        'product_id' => $validated['product_id'],
        'quantity' => $validated['quantity'],
        'total_price' => $total_price,
    ]);

    return response()->json($order);
    }

    public function destroy($id)
{
    $order = Order::findOrFail($id);
    $order->delete();

    return response()->json(['message' => 'Order deleted successfully']);
}

}