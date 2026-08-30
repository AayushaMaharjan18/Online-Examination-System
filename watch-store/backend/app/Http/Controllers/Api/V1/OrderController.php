<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('user_id', $request->user()?->id)
            ->with('items.product')
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders->map(function (Order $order) {
                $order->status_label = match ($order->status) {
                    'completed' => 'Completed',
                    'pending' => 'Pending',
                    'cancelled' => 'Cancelled',
                    default => ucfirst($order->status),
                };

                return $order;
            }),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_district' => 'required|string|max:255',
            'shipping_municipality' => 'required|string|max:255',
            'shipping_street' => 'required|string|max:255',
            'payment_method' => 'nullable|string|in:cod,khalti,esewa',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subtotal = collect($request->items)->sum('total');

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'user_id' => $request->user()?->id,
            'subtotal' => $subtotal,
            'shipping_cost' => 0,
            'tax' => 0,
            'discount' => 0,
            'total' => $subtotal,
            'status' => 'pending',
            'payment_method' => $request->payment_method ?? 'cod',
            'payment_status' => 'unpaid',
            'shipping_name' => $request->shipping_name,
            'shipping_phone' => $request->shipping_phone,
            'shipping_district' => $request->shipping_district,
            'shipping_municipality' => $request->shipping_municipality,
            'shipping_street' => $request->shipping_street,
            'shipping_ward' => $request->shipping_ward ?? '',
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'product_sku' => null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total'],
            ]);
        }

        return response()->json([
            'message' => 'Order placed successfully',
            'data' => $order->load('items'),
        ], 201);
    }
}
