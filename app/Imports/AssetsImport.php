<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class AssetsImport implements ToCollection, WithHeadingRow, WithValidation
{
    private int $importedCount = 0;
    private array $errors = [];

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                // Find or create category
                $category = null;
                if (!empty($row['category'])) {
                    $category = AssetCategory::firstOrCreate(['name' => $row['category']], [
                        'company_id' => auth()->user()->company_id,
                        'color' => '#6366f1',
                        'icon' => 'bi-box'
                    ]);
                }

                // Find or create location
                $location = null;
                if (!empty($row['location'])) {
                    $location = Location::firstOrCreate(['name' => $row['location']], [
                        'company_id' => auth()->user()->company_id,
                    ]);
                }

                // Find or create vendor
                $vendor = null;
                if (!empty($row['vendor'])) {
                    $vendor = Vendor::firstOrCreate(['name' => $row['vendor']], [
                        'company_id' => auth()->user()->company_id,
                    ]);
                }

                // Find assignee
                $assignee = null;
                if (!empty($row['assigned_to'])) {
                    $assignee = User::where('name', $row['assigned_to'])
                                   ->orWhere('email', $row['assigned_to'])
                                   ->first();
                }

                // Create asset
                $asset = Asset::create([
                    'company_id' => auth()->user()->company_id,
                    'asset_code' => $row['asset_code'] ?: Asset::generateAssetCode(),
                    'name' => $row['name'],
                    'description' => $row['description'] ?? null,
                    'category_id' => $category?->id,
                    'location_id' => $location?->id,
                    'vendor_id' => $vendor?->id,
                    'assignee_id' => $assignee?->id,
                    'serial_number' => $row['serial_number'] ?? null,
                    'purchase_date' => !empty($row['purchase_date']) ? Carbon::parse($row['purchase_date']) : null,
                    'purchase_cost' => !empty($row['purchase_cost']) ? (float) $row['purchase_cost'] : null,
                    'warranty_expiry' => !empty($row['warranty_expiry']) ? Carbon::parse($row['warranty_expiry']) : null,
                    'status' => $row['status'] ?? 'IN_STOCK',
                    'depreciation_method' => 'STRAIGHT_LINE',
                    'created_by' => auth()->id(),
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Row error: " . $e->getMessage();
            }
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'asset_code' => 'nullable|string|max:255|unique:assets,asset_code',
            'status' => 'nullable|in:IN_STOCK,ASSIGNED,MAINTENANCE,RETIRED,LOST',
            'purchase_cost' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get the number of imported assets
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Get import errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
