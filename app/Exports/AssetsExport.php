<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class AssetsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $query = Asset::with(['category', 'location', 'vendor', 'assignee'])
                     ->forCompany();

        // Apply filters
        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['location_id'])) {
            $query->where('location_id', $this->filters['location_id']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Asset Code',
            'Name',
            'Description',
            'Category',
            'Status',
            'Location',
            'Assigned To',
            'Serial Number',
            'Purchase Date',
            'Purchase Cost',
            'Current Value',
            'Vendor',
            'Warranty Expiry',
            'Created At',
        ];
    }

    /**
     * @param Asset $asset
     * @return array
     */
    public function map($asset): array
    {
        return [
            $asset->asset_code,
            $asset->name,
            $asset->description,
            $asset->category?->name,
            $asset->status_label,
            $asset->location?->name,
            $asset->assignee?->name,
            $asset->serial_number,
            $asset->purchase_date?->format('Y-m-d'),
            $asset->purchase_cost,
            $asset->book_value,
            $asset->vendor?->name,
            $asset->warranty_expiry?->format('Y-m-d'),
            $asset->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
