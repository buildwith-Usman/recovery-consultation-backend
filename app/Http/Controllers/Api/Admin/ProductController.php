<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDosage;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get list of products with pagination and filters
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit') ?? 10;
            $categoryId = $request->input('category_id');
            $isVisible = $request->input('is_visible');
            $search = $request->input('search');
            $sortBy = $request->input('sort_by') ?? 'created_at';
            $sortOrder = $request->input('sort_order') ?? 'desc';

            $products = Product::with(['image', 'images.file', 'category', 'creator', 'dosages'])
                ->withCount('featuredByUsers')
                ->when($categoryId, function ($q) use ($categoryId) {
                    $q->byCategory($categoryId);
                })
                ->when(isset($isVisible), function ($q) use ($isVisible) {
                    $q->where('is_visible', $isVisible);
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
     * Create a new product
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'medicine_name' => 'required|string|max:255',
                'image_id' => 'nullable|exists:files,id',
                'category_id' => 'required|exists:categories,id',
                'ingredients' => 'nullable|string',
                'discount_type' => 'nullable|in:percentage,flat',
                'discount_value' => 'nullable|numeric|min:0',
                'how_to_use' => 'nullable|string',
                'description' => 'nullable|string',
                'is_visible' => 'nullable|boolean',
                'is_temporarily_hidden' => 'nullable|boolean',
                'image_ids' => 'nullable|array',
                'image_ids.*' => 'exists:files,id',
                'dosages' => 'required|array|min:1',
                'dosages.*.name' => 'required|string|max:255',
                'dosages.*.price' => 'required|numeric|min:0',
                'dosages.*.stock_quantity' => 'nullable|integer|min:0',
            ]);

            // Validate discount
            if (isset($validatedData['discount_type']) && !isset($validatedData['discount_value'])) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['discount_value is required when discount_type is set']
                ], 422);
            }

            if (isset($validatedData['discount_type']) && $validatedData['discount_type'] === 'percentage' && $validatedData['discount_value'] > 100) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['discount_value cannot exceed 100 for percentage discount']
                ], 422);
            }

            // Add authenticated admin user as creator
            $validatedData['created_by'] = auth()->user()->id;

            $imageIds = $validatedData['image_ids'] ?? [];
            $dosages = $validatedData['dosages'];
            unset($validatedData['image_ids'], $validatedData['dosages']);

            $product = Product::create($validatedData);

            // Create additional gallery images
            foreach ($imageIds as $index => $fileId) {
                $product->images()->create([
                    'file_id' => $fileId,
                    'sort_order' => $index,
                ]);
            }

            // Create dosage variations
            foreach ($dosages as $index => $dosage) {
                $productDosage = $product->dosages()->create([
                    'name' => $dosage['name'],
                    'price' => $dosage['price'],
                    'stock_quantity' => $dosage['stock_quantity'] ?? 0,
                    'sort_order' => $index,
                ]);
                $productDosage->updateAvailabilityStatus();
            }

            // Load relationships
            $product->load(['image', 'images.file', 'category', 'creator', 'dosages']);

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);

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
                'message' => 'Product creation failed',
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
            $product = Product::with(['image', 'images.file', 'category', 'creator', 'dosages'])->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                    'errors' => ['Product does not exist']
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
     * Update an existing product
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                    'errors' => ['Product does not exist']
                ], 404);
            }

            $validatedData = $request->validate([
                'medicine_name' => 'nullable|string|max:255',
                'image_id' => 'nullable|exists:files,id',
                'category_id' => 'nullable|exists:categories,id',
                'ingredients' => 'nullable|string',
                'discount_type' => 'nullable|in:percentage,flat',
                'discount_value' => 'nullable|numeric|min:0',
                'how_to_use' => 'nullable|string',
                'description' => 'nullable|string',
                'is_visible' => 'nullable|boolean',
                'is_temporarily_hidden' => 'nullable|boolean',
                'image_ids' => 'nullable|array',
                'image_ids.*' => 'exists:files,id',
                'dosages' => 'nullable|array|min:1',
                'dosages.*.name' => 'required|string|max:255',
                'dosages.*.price' => 'required|numeric|min:0',
                'dosages.*.stock_quantity' => 'nullable|integer|min:0',
            ]);

            // Validate discount
            if (isset($validatedData['discount_type']) && $validatedData['discount_type'] === 'percentage' && isset($validatedData['discount_value']) && $validatedData['discount_value'] > 100) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['discount_value cannot exceed 100 for percentage discount']
                ], 422);
            }

            // Extract image_ids and dosages before updating product fields
            $imageIds = null;
            if (array_key_exists('image_ids', $validatedData)) {
                $imageIds = $validatedData['image_ids'];
                unset($validatedData['image_ids']);
            }

            $dosages = null;
            if (array_key_exists('dosages', $validatedData)) {
                $dosages = $validatedData['dosages'];
                unset($validatedData['dosages']);
            }

            // Update only provided fields
            $product->update(array_filter($validatedData, function ($value) {
                return !is_null($value);
            }));

            // Replace gallery images if image_ids was provided
            if ($imageIds !== null) {
                $product->images()->delete();
                foreach ($imageIds as $index => $fileId) {
                    $product->images()->create([
                        'file_id' => $fileId,
                        'sort_order' => $index,
                    ]);
                }
            }

            // Replace dosages if provided
            if ($dosages !== null) {
                $product->dosages()->delete();
                foreach ($dosages as $index => $dosage) {
                    $productDosage = $product->dosages()->create([
                        'name' => $dosage['name'],
                        'price' => $dosage['price'],
                        'stock_quantity' => $dosage['stock_quantity'] ?? 0,
                        'sort_order' => $index,
                    ]);
                    $productDosage->updateAvailabilityStatus();
                }
            }

            // Load relationships
            $product->load(['image', 'images.file', 'category', 'creator', 'dosages']);

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $product
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
                'message' => 'Product update failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Delete a product
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                    'errors' => ['Product does not exist']
                ], 404);
            }

            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Product deletion failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
