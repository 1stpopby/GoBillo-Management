<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetTag;
use App\Models\Location;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Asset::class);
        
        $query = Asset::with(['category', 'location', 'vendor', 'assignee', 'tags'])
                     ->forCompany();

        // Apply search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['asset_code', 'name', 'status', 'purchase_date', 'purchase_cost', 'created_at'];
        $allowedDirections = ['asc', 'desc'];
        
        if (in_array($sortBy, $allowedSorts) && in_array($sortDirection, $allowedDirections)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $assets = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $assets
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Asset::class);

        $companyId = auth()->user()->company_id;
        
        $validated = $request->validate([
            'asset_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('assets', 'asset_code')->where('company_id', $companyId)
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => [
                'required',
                Rule::exists('asset_categories', 'id')->where('company_id', $companyId)
            ],
            'location_id' => [
                'required', 
                Rule::exists('locations', 'id')->where('company_id', $companyId)
            ],
            'vendor_id' => [
                'required',
                Rule::exists('vendors', 'id')->where('company_id', $companyId)
            ],
            'purchase_date' => 'required|date|before_or_equal:today',
            'purchase_cost' => 'required|numeric|min:0|max:999999999.99',
            'status' => 'required|in:IN_STOCK,ASSIGNED,MAINTENANCE,RETIRED,LOST',
            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('assets', 'serial_number')->where('company_id', $companyId)
            ],
            'warranty_expiry' => 'nullable|date|after:purchase_date',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        $asset = Asset::create($validated);
        $asset->load(['category', 'location', 'vendor']);

        return response()->json([
            'success' => true,
            'message' => 'Asset created successfully',
            'data' => $asset
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $asset = Asset::with(['category', 'location', 'vendor', 'assignee', 'tags'])
                     ->forCompany()
                     ->findOrFail($id);

        $this->authorize('view', $asset);

        return response()->json([
            'success' => true,
            'data' => $asset
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $asset = Asset::forCompany()->findOrFail($id);
        $this->authorize('update', $asset);

        $companyId = auth()->user()->company_id;
        
        $validated = $request->validate([
            'asset_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('assets', 'asset_code')->where('company_id', $companyId)->ignore($asset->id)
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => [
                'required',
                Rule::exists('asset_categories', 'id')->where('company_id', $companyId)
            ],
            'location_id' => [
                'required',
                Rule::exists('locations', 'id')->where('company_id', $companyId)
            ],
            'vendor_id' => [
                'required',
                Rule::exists('vendors', 'id')->where('company_id', $companyId)
            ],
            'purchase_date' => 'required|date|before_or_equal:today',
            'purchase_cost' => 'required|numeric|min:0|max:999999999.99',
            'status' => 'required|in:IN_STOCK,ASSIGNED,MAINTENANCE,RETIRED,LOST',
            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('assets', 'serial_number')->where('company_id', $companyId)->ignore($asset->id)
            ],
            'warranty_expiry' => 'nullable|date|after:purchase_date',
            'notes' => 'nullable|string',
        ]);

        $asset->update($validated);
        $asset->load(['category', 'location', 'vendor', 'assignee']);

        return response()->json([
            'success' => true,
            'message' => 'Asset updated successfully',
            'data' => $asset
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $asset = Asset::forCompany()->findOrFail($id);
        $this->authorize('delete', $asset);

        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset deleted successfully'
        ]);
    }

    /**
     * Get lookup data for dropdowns
     */
    public function lookups(): JsonResponse
    {
        $user = auth()->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => AssetCategory::active()->orderBy('name')->get(),
                'locations' => Location::active()->orderBy('name')->get(),
                'vendors' => Vendor::active()->orderBy('name')->get(),
                'tags' => AssetTag::orderBy('name')->get(),
                'users' => User::forCompany()->orderBy('name')->get(),
                'statuses' => [
                    'IN_STOCK' => 'In Stock',
                    'ASSIGNED' => 'Assigned',
                    'MAINTENANCE' => 'Maintenance',
                    'RETIRED' => 'Retired',
                    'LOST' => 'Lost'
                ]
            ]
        ]);
    }
}