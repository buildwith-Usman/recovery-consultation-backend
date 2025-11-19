<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit') ?? 10;
            $status = $request->input('status');
            $paymentStatus = $request->input('payment_status');
            $search = $request->input('search');

            $orders = Order::with(['user', 'items.product', 'prescription'])
                ->when($status, function ($q) use ($status) {
                    $q->byStatus($status);
                })
                ->when($paymentStatus, function ($q) use ($paymentStatus) {
                    $q->where('payment_status', $paymentStatus);
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('order_number', 'like', '%' . $search . '%')
                              ->orWhere('customer_name', 'like', '%' . $search . '%')
                              ->orWhere('customer_phone', 'like', '%' . $search . '%');
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'message' => 'Orders list',
                'data' => $orders->items(),
                'errors' => null,
                'pagination' => [
                    'total' => $orders->total(),
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'last_page' => $orders->lastPage(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve orders',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($orderNumber)
    {
        try {
            $order = Order::with(['user', 'items.product', 'prescription.doctor'])
                ->where('order_number', $orderNumber)
                ->first();

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                    'errors' => ['Order does not exist']
                ], 404);
            }

            return response()->json([
                'message' => 'Order details retrieved successfully',
                'data' => $order
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve order',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function updateStatus(Request $request, $orderNumber)
    {
        try {
            $validatedData = $request->validate([
                'order_status' => 'required|in:placed,dispatched,delivered,completed,cancelled',
                'payment_status' => 'nullable|in:pending,paid,failed'
            ]);

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                    'errors' => ['Order does not exist']
                ], 404);
            }

            // Update timestamps based on status
            if ($validatedData['order_status'] === 'dispatched' && !$order->dispatched_at) {
                $order->dispatched_at = now();
            }
            if ($validatedData['order_status'] === 'delivered' && !$order->delivered_at) {
                $order->delivered_at = now();
            }
            if ($validatedData['order_status'] === 'completed' && !$order->completed_at) {
                $order->completed_at = now();
            }

            $order->order_status = $validatedData['order_status'];

            if (isset($validatedData['payment_status'])) {
                $order->payment_status = $validatedData['payment_status'];
            }

            $order->save();

            return response()->json([
                'message' => 'Order status updated successfully',
                'data' => $order
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorsList = [];
            foreach ($e->errors() as $err) {
                $errorsList = array_merge($errorsList, $err);
            }
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errorsList
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Order status update failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
