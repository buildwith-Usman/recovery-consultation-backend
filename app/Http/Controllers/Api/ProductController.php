<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get list of visible products for customers
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit') ?? 10;
            $categoryId = $request->input('category_id');
            $search = $request->input('search');
            $sortBy = $request->input('sort_by') ?? 'created_at';
            $sortOrder = $request->input('sort_order') ?? 'desc';

            $products = Product::with(['image', 'images.file', 'category'])
                ->visibleToCustomers()
                ->when($categoryId, function ($q) use ($categoryId) {
                    $q->byCategory($categoryId);
                })
                ->when($search, function ($q) use ($search) {
                    $q->search($search);
                })
                ->orderBy($sortBy, $sortOrder)
                ->paginate($limit);

            return response()->json([
                'message' => 'Products list',
                'data' => $products->items(),
                'errors' => null,
                'pagination' => [
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve products',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get a single product by ID
     */
    public function show($id)
    {
        try {
            $product = Product::with(['image', 'images.file', 'category'])
                ->visibleToCustomers()
                ->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                    'errors' => ['Product does not exist or is not available']
                ], 404);
            }

            return response()->json([
                'message' => 'Product details retrieved successfully',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve product',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get products by category
     */
    public function byCategory($categoryId, Request $request)
    {
        try {
            $limit = $request->input('limit') ?? 10;
            $search = $request->input('search');

            $products = Product::with(['image', 'images.file', 'category'])
                ->visibleToCustomers()
                ->byCategory($categoryId)
                ->when($search, function ($q) use ($search) {
                    $q->search($search);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'message' => 'Products by category',
                'data' => $products->items(),
                'errors' => null,
                'pagination' => [
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve products',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
