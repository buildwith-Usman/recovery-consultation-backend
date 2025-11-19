<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get list of categories with pagination
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit') ?? 10;
            $status = $request->input('status');

            $categories = Category::withCount('products')
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->orderBy('name', 'asc')
                ->paginate($limit);

            return response()->json([
                'message' => 'Categories list',
                'data' => $categories->items(),
                'errors' => null,
                'pagination' => [
                    'total' => $categories->total(),
                    'current_page' => $categories->currentPage(),
                    'per_page' => $categories->perPage(),
                    'last_page' => $categories->lastPage(),
                    'from' => $categories->firstItem(),
                    'to' => $categories->lastItem()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve categories',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Create a new category
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive'
            ]);

            $category = Category::create($validatedData);

            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category
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
                'message' => 'Category creation failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get a single category by ID
     */
    public function show($id)
    {
        try {
            $category = Category::withCount('products')->find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found',
                    'errors' => ['Category does not exist']
                ], 404);
            }

            return response()->json([
                'message' => 'Category details retrieved successfully',
                'data' => $category
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve category',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Update an existing category
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found',
                    'errors' => ['Category does not exist']
                ], 404);
            }

            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
                'status' => 'nullable|in:active,inactive'
            ]);

            $category->update(array_filter($validatedData, function ($value) {
                return !is_null($value);
            }));

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category
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
                'message' => 'Category update failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Delete a category
     */
    public function destroy($id)
    {
        try {
            $category = Category::withCount('products')->find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found',
                    'errors' => ['Category does not exist']
                ], 404);
            }

            // Check if category has products
            if ($category->products_count > 0) {
                return response()->json([
                    'message' => 'Cannot delete category',
                    'errors' => ['Category has ' . $category->products_count . ' product(s). Please reassign or delete them first.']
                ], 422);
            }

            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Category deletion failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get active categories (for dropdown)
     */
    public function activeList(Request $request)
    {
        try {
            $categories = Category::active()
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);

            return response()->json([
                'message' => 'Active categories list',
                'data' => $categories,
                'errors' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve active categories',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
