<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\ProductDosage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get list of user's orders
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $limit = $request->input('limit') ?? 10;
            $status = $request->input('status'); // all, in_progress, delivered

            $orders = Order::with(['items.product', 'prescription'])
                ->forUser($user->id)
                ->when($status === 'in_progress', function ($q) {
                    $q->whereIn('order_status', ['placed', 'dispatched']);
                })
                ->when($status === 'delivered', function ($q) {
                    $q->whereIn('order_status', ['delivered', 'completed']);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            // Format response to match UI requirements
            $formattedOrders = $orders->getCollection()->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'date' => $order->placed_at->format('F j, Y'),
                    'items_count' => $order->items->count(),
                    'total_amount' => $order->total_amount,
                    'order_status' => $order->order_status,
                    'payment_status' => $order->payment_status
                ];
            });

            return response()->json([
                'message' => 'Orders list',
                'data' => $formattedOrders,
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

    /**
     * Create order with items (cart managed by frontend)
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.product_dosage_id' => 'required|exists:product_dosages,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.dosage_info' => 'nullable|string',
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'delivery_address' => 'required|string',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'payment_method' => 'required|in:jazzcash,easypaisa,debit_card',
                'prescription_id' => 'nullable|exists:prescriptions,id',
                'delivery_fee' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'other_fees' => 'nullable|numeric|min:0'
            ]);

            $user = auth()->user();

            DB::beginTransaction();

            try {
                // Calculate subtotal from items
                $subtotal = 0;
                $orderItems = [];

                foreach ($validatedData['items'] as $item) {
                    $product = Product::find($item['product_id']);

                    // Check if product is visible and available
                    if (!$product->is_visible || $product->is_temporarily_hidden) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Product not available',
                            'errors' => [$product->medicine_name . ' is currently not available']
                        ], 422);
                    }

                    // Fetch dosage and verify it belongs to the product
                    $dosage = ProductDosage::find($item['product_dosage_id']);

                    if ($dosage->product_id !== $product->id) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Invalid dosage',
                            'errors' => ['The selected dosage does not belong to ' . $product->medicine_name]
                        ], 422);
                    }

                    // Check stock availability on dosage
                    if ($dosage->stock_quantity < $item['quantity']) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Insufficient stock',
                            'errors' => [$product->medicine_name . ' (' . $dosage->name . ') has only ' . $dosage->stock_quantity . ' items available']
                        ], 422);
                    }

                    $price = $dosage->final_price;
                    $itemTotal = $item['quantity'] * $price;
                    $subtotal += $itemTotal;

                    $orderItems[] = [
                        'product' => $product,
                        'dosage' => $dosage,
                        'quantity' => $item['quantity'],
                        'price' => $price,
                        'dosage_info' => $item['dosage_info'] ?? null,
                        'item_total' => $itemTotal
                    ];
                }

                // Calculate totals
                $deliveryFee = $validatedData['delivery_fee'] ?? 50; // Default Rs. 50
                $taxAmount = $validatedData['tax_amount'] ?? 0;
                $otherFees = $validatedData['other_fees'] ?? 0;
                $totalAmount = $subtotal + $deliveryFee + $taxAmount + $otherFees;

                // Check if prescription order
                $isPrescriptionOrder = !empty($validatedData['prescription_id']);

                // Create order
                $order = Order::create([
                    'user_id' => $user->id,
                    'subtotal' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'tax_amount' => $taxAmount,
                    'other_fees' => $otherFees,
                    'total_amount' => $totalAmount,
                    'payment_method' => $validatedData['payment_method'],
                    'payment_status' => 'pending',
                    'order_status' => 'placed',
                    'customer_name' => $validatedData['customer_name'],
                    'customer_phone' => $validatedData['customer_phone'],
                    'delivery_address' => $validatedData['delivery_address'],
                    'city' => $validatedData['city'] ?? null,
                    'country' => $validatedData['country'] ?? null,
                    'postal_code' => $validatedData['postal_code'] ?? null,
                    'prescription_id' => $validatedData['prescription_id'] ?? null,
                    'is_prescription_order' => $isPrescriptionOrder
                ]);

                // Create order items and reduce stock
                foreach ($orderItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'product_dosage_id' => $item['dosage']->id,
                        'product_name' => $item['product']->medicine_name,
                        'dosage_name' => $item['dosage']->name,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'dosage_info' => $item['dosage_info'],
                        'item_total' => $item['item_total']
                    ]);

                    // Reduce stock on dosage
                    $item['dosage']->stock_quantity -= $item['quantity'];
                    $item['dosage']->save();
                    $item['dosage']->updateAvailabilityStatus();
                }

                // Update prescription status if prescription order
                if ($isPrescriptionOrder) {
                    $prescription = Prescription::find($validatedData['prescription_id']);
                    $prescription->status = 'dispensed';
                    $prescription->save();
                }

                DB::commit();

                // Load relationships
                $order->load(['items.product', 'items.dosage', 'prescription.doctor']);

                return response()->json([
                    'message' => 'Order placed successfully',
                    'data' => $order
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

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
                'message' => 'Order creation failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get order details by order number
     */
    public function show($orderNumber)
    {
        try {
            $user = auth()->user();

            $order = Order::with(['items.product.image', 'items.dosage', 'prescription.doctor'])
                ->forUser($user->id)
                ->where('order_number', $orderNumber)
                ->first();

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                    'errors' => ['Order does not exist']
                ], 404);
            }

            // Format response
            $response = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'date' => $order->placed_at->format('F j, Y - g:i A'),
                'order_status' => ucfirst($order->order_status),
                'payment_method' => 'Paid via ' . ucfirst(str_replace('_', ' ', $order->payment_method)),
                'payment_status' => ucfirst($order->payment_status),
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'dosage_name' => $item->dosage_name,
                        'dosage_info' => $item->dosage_info,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'item_total' => $item->item_total
                    ];
                }),
                'price_details' => [
                    'subtotal' => $order->subtotal,
                    'delivery_fee' => $order->delivery_fee,
                    'tax_amount' => $order->tax_amount,
                    'other_fees' => $order->other_fees,
                    'total_paid' => $order->total_amount
                ],
                'delivery_address' => [
                    'name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'address' => $order->delivery_address,
                    'city' => $order->city,
                    'country' => $order->country,
                    'postal_code' => $order->postal_code
                ]
            ];

            // Add prescription info if exists
            if ($order->prescription) {
                $response['prescription_info'] = [
                    'prescribed_by' => $order->prescription->doctor->name,
                    'session_date' => $order->prescription->prescription_date->format('F j, Y')
                ];
            }

            return response()->json([
                'message' => 'Order details retrieved successfully',
                'data' => $response
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve order',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get order tracking timeline
     */
    public function track($orderNumber)
    {
        try {
            $user = auth()->user();

            $order = Order::forUser($user->id)
                ->where('order_number', $orderNumber)
                ->first();

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                    'errors' => ['Order does not exist']
                ], 404);
            }

            $timeline = [
                [
                    'status' => 'Order Placed',
                    'date' => $order->placed_at ? $order->placed_at->format('d M, Y') : null,
                    'time' => $order->placed_at ? $order->placed_at->format('h:iA') : null,
                    'completed' => true
                ],
                [
                    'status' => 'Dispatched',
                    'date' => $order->dispatched_at ? $order->dispatched_at->format('d M, Y') : null,
                    'time' => $order->dispatched_at ? $order->dispatched_at->format('h:iA') : null,
                    'completed' => $order->dispatched_at !== null
                ],
                [
                    'status' => 'Delivered',
                    'date' => $order->delivered_at ? $order->delivered_at->format('d M, Y') : null,
                    'time' => $order->delivered_at ? $order->delivered_at->format('h:iA') : null,
                    'completed' => $order->delivered_at !== null
                ],
                [
                    'status' => 'Completed',
                    'date' => $order->completed_at ? $order->completed_at->format('d M, Y') : null,
                    'time' => $order->completed_at ? $order->completed_at->format('h:iA') : null,
                    'completed' => $order->completed_at !== null
                ]
            ];

            return response()->json([
                'message' => 'Order tracking retrieved successfully',
                'data' => [
                    'order_number' => $order->order_number,
                    'current_status' => ucfirst($order->order_status),
                    'timeline' => $timeline
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve order tracking',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
