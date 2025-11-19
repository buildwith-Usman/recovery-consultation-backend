<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdBanner;
use Illuminate\Http\Request;

class AdBannerController extends Controller
{
    /**
     * Get list of ad banners with pagination and filters
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit') ?? 10;
            $status = $request->input('status');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            $adBanners = AdBanner::with(['image', 'creator'])
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->when($dateFrom, function ($q) use ($dateFrom) {
                    $q->where('start_date', '>=', $dateFrom);
                })
                ->when($dateTo, function ($q) use ($dateTo) {
                    $q->where(function ($query) use ($dateTo) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '<=', $dateTo);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'message' => 'Ad banners list',
                'data' => $adBanners->items(),
                'errors' => null,
                'pagination' => [
                    'total' => $adBanners->total(),
                    'current_page' => $adBanners->currentPage(),
                    'per_page' => $adBanners->perPage(),
                    'last_page' => $adBanners->lastPage(),
                    'from' => $adBanners->firstItem(),
                    'to' => $adBanners->lastItem()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve ad banners',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Create a new ad banner
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'image_id' => 'nullable|exists:files,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'required|in:active,inactive'
            ]);

            // Add authenticated admin user as creator
            $validatedData['created_by'] = auth()->user()->id;

            $adBanner = AdBanner::create($validatedData);

            // Load relationships
            $adBanner->load(['image', 'creator']);

            return response()->json([
                'message' => 'Ad banner created successfully',
                'data' => $adBanner
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
                'message' => 'Ad banner creation failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get a single ad banner by ID
     */
    public function show($id)
    {
        try {
            $adBanner = AdBanner::with(['image', 'creator'])->find($id);

            if (!$adBanner) {
                return response()->json([
                    'message' => 'Ad banner not found',
                    'errors' => ['Ad banner does not exist']
                ], 404);
            }

            return response()->json([
                'message' => 'Ad banner details retrieved successfully',
                'data' => $adBanner
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve ad banner',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Update an existing ad banner
     */
    public function update(Request $request, $id)
    {
        try {
            $adBanner = AdBanner::find($id);

            if (!$adBanner) {
                return response()->json([
                    'message' => 'Ad banner not found',
                    'errors' => ['Ad banner does not exist']
                ], 404);
            }

            $validatedData = $request->validate([
                'title' => 'nullable|string|max:255',
                'image_id' => 'nullable|exists:files,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|in:active,inactive'
            ]);

            // Update only provided fields
            $adBanner->update(array_filter($validatedData, function ($value) {
                return !is_null($value);
            }));

            // Load relationships
            $adBanner->load(['image', 'creator']);

            return response()->json([
                'message' => 'Ad banner updated successfully',
                'data' => $adBanner
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
                'message' => 'Ad banner update failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Delete an ad banner
     */
    public function destroy($id)
    {
        try {
            $adBanner = AdBanner::find($id);

            if (!$adBanner) {
                return response()->json([
                    'message' => 'Ad banner not found',
                    'errors' => ['Ad banner does not exist']
                ], 404);
            }

            $adBanner->delete();

            return response()->json([
                'message' => 'Ad banner deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ad banner deletion failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get active ad banners (public endpoint)
     */
    public function activeList(Request $request)
    {
        try {
            $limit = $request->input('limit') ?? 10;

            $adBanners = AdBanner::with(['image'])
                ->active()
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'message' => 'Active ad banners list',
                'data' => $adBanners->items(),
                'errors' => null,
                'pagination' => [
                    'total' => $adBanners->total(),
                    'current_page' => $adBanners->currentPage(),
                    'per_page' => $adBanners->perPage(),
                    'last_page' => $adBanners->lastPage(),
                    'from' => $adBanners->firstItem(),
                    'to' => $adBanners->lastItem()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve active ad banners',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
