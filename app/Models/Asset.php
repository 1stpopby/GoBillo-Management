<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class Asset extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'company_id',
        'asset_code',
        'name',
        'description',
        'category_id',
        'location_id',
        'vendor_id',
        'purchase_date',
        'purchase_cost',
        'depreciation_method',
        'depreciation_life_months',
        'status',
        'assignee_id',
        'department',
        'serial_number',
        'warranty_expiry',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (auth()->check()) {
                $asset->created_by = auth()->id();
                if (!$asset->company_id && auth()->user()->company_id) {
                    $asset->company_id = auth()->user()->company_id;
                }
            }
        });

        static::updating(function ($asset) {
            if (auth()->check()) {
                $asset->updated_by = auth()->id();
            }
        });
    }

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['asset_code', 'name', 'status', 'assignee_id', 'location_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(AssetTag::class, 'asset_asset_tag');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // Scopes
    public function scopeForCompany(Builder $query, $companyId = null): Builder
    {
        $companyId = $companyId ?? auth()->user()?->company_id ?? null;
        
        if ($companyId === null) {
            // For superadmin or console contexts, don't filter
            return $query;
        }

        return $query->where('company_id', $companyId);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('asset_code', 'like', "%{$term}%")
              ->orWhere('serial_number', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeCategory(Builder $query, ?int $categoryId): Builder
    {
        if (!$categoryId) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    public function scopeLocation(Builder $query, ?int $locationId): Builder
    {
        if (!$locationId) {
            return $query;
        }

        return $query->where('location_id', $locationId);
    }

    public function scopeTag(Builder $query, ?string $tagSlug): Builder
    {
        if (!$tagSlug) {
            return $query;
        }

        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('slug', $tagSlug);
        });
    }

    public function scopeDateBetween(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->where('purchase_date', '>=', $from);
        }

        if ($to) {
            $query->where('purchase_date', '<=', $to);
        }

        return $query;
    }

    public function scopeAssignedTo(Builder $query, ?int $userId): Builder
    {
        if (!$userId) {
            return $query;
        }

        return $query->where('assignee_id', $userId);
    }

    // Accessors
    public function getPurchaseCostFormattedAttribute(): string
    {
        return '$' . number_format($this->purchase_cost, 2);
    }

    public function getQrCodeUrlAttribute(): string
    {
        return route('assets.show', $this);
    }

    public function getBookValueAttribute(): float
    {
        if ($this->depreciation_method === 'NONE') {
            return (float) $this->purchase_cost;
        }

        if ($this->depreciation_method === 'STRAIGHT_LINE' && $this->depreciation_life_months) {
            $monthsSincePurchase = Carbon::parse($this->purchase_date)->diffInMonths(now());
            $monthlyDepreciation = $this->purchase_cost / $this->depreciation_life_months;
            $totalDepreciation = $monthlyDepreciation * $monthsSincePurchase;
            
            return max(0, (float) ($this->purchase_cost - $totalDepreciation));
        }

        return (float) $this->purchase_cost;
    }

    public function getBookValueFormattedAttribute(): string
    {
        return '$' . number_format($this->getBookValueAttribute(), 2);
    }

    public function getMonthlyDepreciationAttribute(): float
    {
        if ($this->depreciation_method === 'STRAIGHT_LINE' && $this->depreciation_life_months) {
            return (float) ($this->purchase_cost / $this->depreciation_life_months);
        }

        return 0.0;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'IN_STOCK' => 'bg-blue-100 text-blue-800',
            'ASSIGNED' => 'bg-green-100 text-green-800',
            'MAINTENANCE' => 'bg-yellow-100 text-yellow-800',
            'RETIRED' => 'bg-gray-100 text-gray-800',
            'LOST' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'IN_STOCK' => 'In Stock',
            'ASSIGNED' => 'Assigned',
            'MAINTENANCE' => 'Maintenance',
            'RETIRED' => 'Retired',
            'LOST' => 'Lost',
            default => $this->status,
        };
    }

    public function getWarrantyStatusAttribute(): array
    {
        if (!$this->warranty_expiry) {
            return ['status' => 'none', 'message' => 'No warranty'];
        }

        $now = now();
        $expiry = Carbon::parse($this->warranty_expiry);

        if ($expiry->isPast()) {
            return ['status' => 'expired', 'message' => 'Warranty expired'];
        }

        $daysUntilExpiry = $now->diffInDays($expiry);

        if ($daysUntilExpiry <= 30) {
            return ['status' => 'expiring', 'message' => "Expires in {$daysUntilExpiry} days"];
        }

        return ['status' => 'active', 'message' => "Expires on {$expiry->format('M j, Y')}"];
    }

    // Static methods
    public static function generateAssetCode(?string $prefix = null): string
    {
        $prefix = $prefix ?: 'AST';
        $lastAsset = static::where('asset_code', 'like', $prefix . '-%')
                          ->orderBy('asset_code', 'desc')
                          ->first();

        if (!$lastAsset) {
            return $prefix . '-000001';
        }

        $lastNumber = (int) substr($lastAsset->asset_code, strlen($prefix) + 1);
        $nextNumber = $lastNumber + 1;

        return $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function getStatuses(): array
    {
        return [
            'IN_STOCK' => 'In Stock',
            'ASSIGNED' => 'Assigned',
            'MAINTENANCE' => 'Maintenance',
            'RETIRED' => 'Retired',
            'LOST' => 'Lost',
        ];
    }

    public static function getDepreciationMethods(): array
    {
        return [
            'NONE' => 'No Depreciation',
            'STRAIGHT_LINE' => 'Straight Line',
        ];
    }
}