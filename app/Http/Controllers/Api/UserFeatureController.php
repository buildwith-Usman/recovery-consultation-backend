<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class UserFeatureController extends Controller
{
    /**
     * Get the authenticated user's featured products.
     */
    public function index()
    {
        $user = Auth::user();
        $features = $user->featureProducts()
            ->with(['image', 'category'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $features
        ]);
    }

    /**
     * Add a product to the user's features.
     */
    public function store($productId)
    {
        $user = Auth::user();

        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        if ($user->featureProducts()->where('product_id', $productId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in features'
            ], 409);
        }

        $user->featureProducts()->attach($productId);

        return response()->json([
            'success' => true,
            'message' => 'Product added to features'
        ], 201);
    }

    /**
     * Remove a product from the user's features.
     */
    public function destroy($productId)
    {
        $user = Auth::user();

        if (!$user->featureProducts()->where('product_id', $productId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not in features'
            ], 404);
        }

        $user->featureProducts()->detach($productId);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from features'
        ]);
    }

    /**
     * Check if a product is in the user's features.
     */
    public function check($productId)
    {
        $user = Auth::user();
        $isFeature = $user->featureProducts()->where('product_id', $productId)->exists();

        return response()->json([
            'success' => true,
            'is_feature' => $isFeature
        ]);
    }
}
