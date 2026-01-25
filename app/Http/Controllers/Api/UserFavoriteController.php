<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFavoriteController extends Controller
{
    /**
     * Get the authenticated user's favorite products.
     */
    public function index()
    {
        $user = Auth::user();
        $favorites = $user->favoriteProducts()
            ->with(['image', 'category'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $favorites
        ]);
    }

    /**
     * Add a product to the user's favorites.
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

        if ($user->favoriteProducts()->where('product_id', $productId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in favorites'
            ], 409);
        }

        $user->favoriteProducts()->attach($productId);

        return response()->json([
            'success' => true,
            'message' => 'Product added to favorites'
        ], 201);
    }

    /**
     * Remove a product from the user's favorites.
     */
    public function destroy($productId)
    {
        $user = Auth::user();

        if (!$user->favoriteProducts()->where('product_id', $productId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not in favorites'
            ], 404);
        }

        $user->favoriteProducts()->detach($productId);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from favorites'
        ]);
    }

    /**
     * Check if a product is in the user's favorites.
     */
    public function check($productId)
    {
        $user = Auth::user();
        $isFavorite = $user->favoriteProducts()->where('product_id', $productId)->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ]);
    }
}
