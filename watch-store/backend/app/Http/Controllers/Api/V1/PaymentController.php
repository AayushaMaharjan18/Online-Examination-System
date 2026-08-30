<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Verify a Khalti payment token for an order and mark it paid.
     */
    public function khaltiVerify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'order_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = Order::findOrFail((int) $request->order_id);

        $secret = config('payments.khalti.secret_key');

        // When no secret key is configured, run in demo mode and simulate success.
        if (blank($secret)) {
            $order->update(['payment_status' => 'paid']);

            return response()->json([
                'message' => 'Payment verified (demo mode)',
                'data' => $order,
            ]);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $secret,
            'Content-Type' => 'application/json',
        ])->post(config('payments.khalti.verify_url'), [
            'token' => $request->token,
            'amount' => (int) round(((float) $order->total) * 100),
        ]);

        if ($response->successful()) {
            $order->update(['payment_status' => 'paid']);

            return response()->json([
                'message' => 'Payment verified',
                'data' => $order,
            ]);
        }

        return response()->json([
            'message' => 'Khalti payment verification failed',
            'error' => $response->json(),
        ], 400);
    }

    /**
     * Build the parameters (and gateway URL) needed to initiate an eSewa payment.
     */
    public function esewaInit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'success_url' => 'required|url',
            'failure_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = Order::findOrFail((int) $request->order_id);
        $total = (int) round((float) $order->total);

        $params = [
            'amt' => $total,
            'pdc' => 0,
            'psc' => 0,
            'txAmt' => 0,
            'tAmt' => $total,
            'pid' => $order->order_number,
            'scd' => config('payments.esewa.merchant_id'),
            'su' => $request->success_url,
            'fu' => $request->failure_url,
        ];

        return response()->json([
            'data' => [
                'action' => config('payments.esewa.gateway_url'),
                'params' => $params,
            ],
        ]);
    }

    /**
     * Verify an eSewa payment after the gateway redirects the customer back.
     */
    public function esewaVerify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pid' => 'required|string',
            'total' => 'required|numeric',
            'refId' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = Order::where('order_number', $request->pid)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $response = Http::asForm()->post(config('payments.esewa.verify_url'), [
            'amt' => (int) $request->total,
            'scd' => config('payments.esewa.merchant_id'),
            'pid' => $request->pid,
            'rid' => $request->refId,
        ]);

        $body = (string) $response->body();

        if ($response->successful() && str_contains($body, 'Success')) {
            $order->update(['payment_status' => 'paid']);

            return response()->json([
                'message' => 'Payment verified',
                'data' => $order,
            ]);
        }

        return response()->json([
            'message' => 'eSewa payment could not be verified',
            'data' => $order,
        ], 400);
    }
}