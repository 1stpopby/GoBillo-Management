<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Requests\ImportAssetsRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\Vendor;
use App\Models\AssetTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetsExport;
use App\Imports\AssetsImport;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Asset::class, 'asset');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'location', 'vendor', 'assignee', 'tags'])
                     ->forCompany()
                     ->withTrashed($request->boolean('show_deleted'));

        // Apply search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        if ($request->filled('category_id')) {
            $query->category($request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->location($request->location_id);
        }

        if ($request->filled('tag')) {
            $query->tag($request->tag);
        }

        if ($request->filled('assignee_id')) {
            $query->assignedTo($request->assignee_id);
        }

        // Date range filter
        if ($request->filled(['date_from', 'date_to'])) {
            $query->dateBetween($request->date_from, $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['asset_code', 'name', 'status', 'purchase_date', 'purchase_cost', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $assets = $query->paginate(15)->withQueryString();

        // Get filter options
        $categories = AssetCategory::active()->orderBy('name')->get();
        $locations = Location::active()->orderBy('name')->get();
        $tags = AssetTag::orderBy('name')->get();
        $users = User::forCompany()->orderBy('name')->get();
        $statuses = Asset::getStatuses();

        return view('assets.index', compact(
            'assets', 
            'categories', 
            'locations', 
            'tags', 
            'users', 
            'statuses'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = AssetCategory::active()->orderBy('name')->get();
        $locations = Location::active()->orderBy('name')->get();
        $vendors = Vendor::active()->orderBy('name')->get();
        $tags = AssetTag::orderBy('name')->get();
        $users = User::forCompany()->orderBy('name')->get();
        $statuses = Asset::getStatuses();
        $depreciationMethods = Asset::getDepreciationMethods();

        return view('assets.create', compact(
            'categories',
            'locations', 
            'vendors',
            'tags',
            'users',
            'statuses',
            'depreciationMethods'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssetRequest $request)
    {
        $asset = Asset::create($request->validated());

        // Sync tags
        if ($request->has('tags')) {
            $asset->tags()->sync($request->tags);
        }

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $asset->load([
            'category', 
            'location', 
            'vendor', 
            'assignee', 
            'tags', 
            'createdBy',
            'updatedBy'
        ]);

        // Get activity log
        $activities = $asset->activities()
                           ->with('causer')
                           ->latest()
                           ->limit(10)
                           ->get();

        return view('assets.show', compact('asset', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $asset->load('tags');
        
        $categories = AssetCategory::active()->orderBy('name')->get();
        $locations = Location::active()->orderBy('name')->get();
        $vendors = Vendor::active()->orderBy('name')->get();
        $tags = AssetTag::orderBy('name')->get();
        $users = User::forCompany()->orderBy('name')->get();
        $statuses = Asset::getStatuses();
        $depreciationMethods = Asset::getDepreciationMethods();

        return view('assets.edit', compact(
            'asset',
            'categories',
            'locations', 
            'vendors',
            'tags',
            'users',
            'statuses',
            'depreciationMethods'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $oldAssignee = $asset->assignee_id;
        
        $asset->update($request->validated());

        // Sync tags
        if ($request->has('tags')) {
            $asset->tags()->sync($request->tags);
        }

        // Fire assignment event if assignee changed
        if ($oldAssignee !== $asset->assignee_id && $asset->assignee_id) {
            event(new \App\Events\AssetAssigned($asset, $asset->assignee));
        }

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);
        $this->authorize('restore', $asset);
        
        $asset->restore();

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset restored successfully.');
    }

    /**
     * Bulk delete assets.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,id'
        ]);

        foreach ($request->asset_ids as $assetId) {
            $asset = Asset::findOrFail($assetId);
            $this->authorize('delete', $asset);
            $asset->delete();
        }

        return redirect()
            ->route('assets.index')
            ->with('success', count($request->asset_ids) . ' assets deleted successfully.');
    }

    /**
     * Bulk restore assets.
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,id'
        ]);

        foreach ($request->asset_ids as $assetId) {
            $asset = Asset::withTrashed()->findOrFail($assetId);
            $this->authorize('restore', $asset);
            $asset->restore();
        }

        return redirect()
            ->route('assets.index')
            ->with('success', count($request->asset_ids) . ' assets restored successfully.');
    }

    /**
     * Show QR code for asset.
     */
    public function showQr(Asset $asset)
    {
        $qrCode = QrCode::size(300)->generate($asset->qr_code_url);
        
        return view('assets.qr', compact('asset', 'qrCode'));
    }

    /**
     * Download QR code for asset.
     */
    public function downloadQr(Asset $asset)
    {
        $qrCode = QrCode::format('png')->size(300)->generate($asset->qr_code_url);
        
        $filename = "qr-{$asset->asset_code}.png";
        
        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export assets.
     */
    public function export(Request $request)
    {
        $this->authorize('export', Asset::class);

        $format = $request->get('format', 'xlsx');
        $filename = 'assets-' . now()->format('Y-m-d-H-i-s') . '.' . $format;

        return Excel::download(new AssetsExport($request->all()), $filename);
    }

    /**
     * Show import form.
     */
    public function importForm()
    {
        $this->authorize('import', Asset::class);
        
        return view('assets.import');
    }

    /**
     * Process import.
     */
    public function import(ImportAssetsRequest $request)
    {
        $import = new AssetsImport();

        try {
            Excel::import($import, $request->file('file'));

            $message = "Import completed successfully. ";
            $message .= "Imported: {$import->getImportedCount()} assets";

            if (count($import->getErrors()) > 0) {
                $message .= ", Errors: " . count($import->getErrors());
                session()->flash('import_errors', $import->getErrors());
            }

            return redirect()
                ->route('assets.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download import template.
     */
    public function template()
    {
        $this->authorize('import', Asset::class);

        $headers = [
            'asset_code',
            'name', 
            'description',
            'category_name',
            'location_name',
            'vendor_name',
            'purchase_date',
            'purchase_cost',
            'depreciation_method',
            'depreciation_life_months',
            'status',
            'assignee_email',
            'department',
            'serial_number',
            'warranty_expiry',
            'notes',
            'tags'
        ];

        $csv = implode(',', $headers) . "\n";
        $csv .= "AST-000001,MacBook Pro 14,Apple MacBook Pro 14-inch,Laptops,Main Office,Apple Inc.,2024-01-15,2499.00,STRAIGHT_LINE,36,IN_STOCK,,IT,ABC123456,2027-01-15,Sample notes,high-priority;under-warranty\n";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="assets-import-template.csv"');
    }

    /**
     * Assign asset to user
     */
    public function assign(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'assignee_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'send_notification' => 'boolean',
        ]);

        $asset = Asset::forCompany()->findOrFail($request->asset_id);
        
        // Check if asset is available for assignment
        if ($asset->status !== 'IN_STOCK') {
            return redirect()->back()->with('error', 'Asset is not available for assignment.');
        }

        // Update asset status and assignee
        $asset->update([
            'status' => 'ASSIGNED',
            'assignee_id' => $request->assignee_id,
            'notes' => $request->notes ? $asset->notes . "\n\n[" . now()->format('Y-m-d H:i') . "] Assigned: " . $request->notes : $asset->notes,
        ]);

        return redirect()->back()->with('success', 'Asset assigned successfully.');
    }

    /**
     * Unassign asset from user
     */
    public function unassign(Request $request, Asset $asset)
    {
        $request->validate([
            'status' => 'required|in:IN_STOCK,MAINTENANCE,RETIRED',
            'assignee_id' => 'nullable',
            'notes' => 'nullable|string|max:1000',
        ]);

        $asset->update([
            'status' => $request->status,
            'assignee_id' => null,
            'notes' => $request->notes ? $asset->notes . "\n\n[" . now()->format('Y-m-d H:i') . "] Unassigned: " . $request->notes : $asset->notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Asset unassigned successfully.']);
    }
}