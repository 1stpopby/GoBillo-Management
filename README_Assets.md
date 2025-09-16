# Assets Management Module

A comprehensive asset management system built for Laravel 11 with full CRUD operations, QR code generation, import/export functionality, and API support.

## Features

- ✅ **Full CRUD Operations** - Create, read, update, delete assets with soft deletes
- ✅ **Related Entities** - Categories, Locations, Vendors, Tags (many-to-many)
- ✅ **User Assignment** - Assign assets to users with department tracking
- ✅ **Depreciation Tracking** - Straight-line depreciation with book value calculation
- ✅ **File Attachments** - Multiple file uploads per asset with validation
- ✅ **Advanced Search & Filtering** - Search by name/code/serial, filter by category/status/location/tags/date ranges
- ✅ **QR Code Generation** - Generate and download QR codes for assets
- ✅ **Import/Export** - CSV/XLSX import/export with validation and error reporting
- ✅ **Activity Logging** - Track all changes with Spatie Activity Log
- ✅ **Role-Based Access Control** - Admin, Manager, Viewer roles with granular permissions
- ✅ **API Support** - RESTful API with Laravel Sanctum authentication
- ✅ **Notifications** - Email notifications for assignments and warranty reminders
- ✅ **Warranty Tracking** - Track warranty expiry with automated reminders

## Installation

### 1. Install Dependencies

The following packages are already installed:

```bash
composer require laravel/sanctum spatie/laravel-permission spatie/laravel-activitylog maatwebsite/excel simplesoftwareio/simple-qrcode
```

### 2. Run Migrations

```bash
php artisan migrate --force
```

### 3. Create Storage Symlink

```bash
php artisan storage:link
```

### 4. Seed Database

```bash
php artisan db:seed --class=AssetsModuleSeeder
```

This creates:
- Asset categories (Laptops, Desktops, Monitors, etc.)
- Locations (Main Office, Warehouse, Remote Office, etc.)
- Vendors (Apple, Dell, HP, etc.)
- Asset tags (High Priority, Under Warranty, etc.)
- Sample assets with relationships
- Admin user: `admin@assets.local` / `password`

### 5. Configure Scheduled Commands

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('assets:warranty-reminders')->daily();
}
```

## Database Schema

### Assets Table
- `id` - Primary key
- `asset_code` - Unique human-readable code (e.g., LPT-000123)
- `name` - Asset name
- `description` - Optional description
- `category_id` - Foreign key to asset_categories
- `location_id` - Foreign key to locations
- `vendor_id` - Foreign key to vendors
- `purchase_date` - Date of purchase
- `purchase_cost` - Purchase cost (decimal 12,2)
- `depreciation_method` - NONE or STRAIGHT_LINE
- `depreciation_life_months` - Depreciation period in months
- `status` - IN_STOCK, ASSIGNED, MAINTENANCE, RETIRED, LOST
- `assignee_id` - Foreign key to users (nullable)
- `department` - Department name (nullable)
- `serial_number` - Serial number (nullable, unique)
- `warranty_expiry` - Warranty expiry date (nullable)
- `notes` - Additional notes (longtext)
- `created_by` / `updated_by` - User tracking
- `created_at` / `updated_at` / `deleted_at` - Timestamps with soft deletes

### Related Tables
- `asset_categories` - Categories with colors and descriptions
- `locations` - Physical locations with full address
- `vendors` - Vendor information with contacts
- `asset_tags` - Tags for flexible categorization
- `asset_asset_tag` - Many-to-many pivot table
- `attachments` - Polymorphic file attachments

## Roles & Permissions

### Roles
- **Admin** - Full access to all features
- **Manager** - Create, update, view, import, export, attachments (no delete)
- **Viewer** - View-only access

### Permissions
- `assets.view` - View asset records
- `assets.create` - Create new assets
- `assets.update` - Update asset records
- `assets.delete` - Delete asset records
- `assets.import` - Import assets from files
- `assets.export` - Export asset data
- `assets.attachments` - Manage asset attachments

## API Endpoints

### Authentication
All API endpoints require Sanctum token authentication:

```bash
# Get token
POST /api/auth/login
{
    "email": "admin@assets.local",
    "password": "password"
}

# Use token in headers
Authorization: Bearer {token}
```

### Assets API

```bash
# List assets with filters
GET /api/v1/assets?q=laptop&status=IN_STOCK&category_id=1&per_page=15

# Get single asset
GET /api/v1/assets/{id}

# Create asset
POST /api/v1/assets
{
    "asset_code": "LPT-000123",
    "name": "MacBook Pro 14",
    "category_id": 1,
    "location_id": 1,
    "vendor_id": 1,
    "purchase_date": "2024-01-15",
    "purchase_cost": 2499.00,
    "status": "IN_STOCK"
}

# Update asset
PUT /api/v1/assets/{id}

# Delete asset
DELETE /api/v1/assets/{id}

# Get lookups (categories, locations, vendors, tags)
GET /api/v1/lookups
```

### API Response Format

```json
{
    "id": 1,
    "asset_code": "LPT-000123",
    "name": "MacBook Pro 14",
    "status": "ASSIGNED",
    "serial_number": "C02XXXXXX",
    "category": {
        "id": 2,
        "name": "Laptops"
    },
    "location": {
        "id": 1,
        "name": "London HQ"
    },
    "vendor": {
        "id": 3,
        "name": "Apple"
    },
    "assignee": {
        "id": 9,
        "name": "Jane Doe",
        "email": "jane@example.com"
    },
    "purchase_date": "2024-09-01",
    "purchase_cost": 1999.00,
    "book_value": 1449.25,
    "warranty_expiry": "2026-09-01",
    "tags": ["design", "pool-a"],
    "links": {
        "self": "/api/v1/assets/1"
    }
}
```

## Import/Export

### Import Template

Download template: `/assets/template`

Required columns:
- `asset_code` - Unique identifier
- `name` - Asset name
- `category_name` - Category name (must exist)
- `location_name` - Location name (must exist)
- `vendor_name` - Vendor name (must exist)
- `purchase_date` - YYYY-MM-DD format
- `purchase_cost` - Numeric value
- `status` - One of: IN_STOCK, ASSIGNED, MAINTENANCE, RETIRED, LOST

Optional columns:
- `description`, `assignee_email`, `department`, `serial_number`, `warranty_expiry`, `notes`, `tags` (semicolon-separated)

### Export Options
- Export filtered results to CSV/XLSX
- Include all asset data with relationships
- Bulk export selected assets

## QR Codes

Each asset has a QR code that links to its detail page:
- View QR code: `/assets/{asset}/qr`
- Download QR code: `/assets/{asset}/download-qr`
- QR codes are generated on-demand using SimpleSoftwareIO/SimpleQrCode

## File Attachments

- Upload multiple files per asset (max 5 files, 10MB each)
- Supported formats: jpg, png, webp, pdf, doc, docx, xlsx, txt
- Files stored in `storage/app/public/assets/`
- Automatic file type detection and icons

## Notifications

### Asset Assignment
When an asset is assigned to a user, they receive an email notification with:
- Asset details
- Assignment date
- Contact information for questions

### Warranty Reminders
Daily scheduled command checks for assets with warranty expiring in 30 days:
- Sends notifications to users with manager role
- Lists assets requiring attention
- Configurable reminder periods

## Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AssetTest.php

# Run with coverage
php artisan test --coverage
```

Test coverage includes:
- Model factories and relationships
- Controller CRUD operations
- API endpoints with authentication
- Import/export functionality
- Notifications and events
- Policy authorization
- Form validation

## Configuration

### File Upload Limits
Modify in `config/filesystems.php` or `.env`:

```env
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=50M
```

### QR Code Settings
QR codes can be customized in the controller:
- Size: 300x300px (configurable)
- Format: PNG/SVG
- Error correction level: Medium

### Depreciation
Currently supports:
- No depreciation (NONE)
- Straight-line depreciation (STRAIGHT_LINE)

Book value calculation:
```php
$monthlyDepreciation = $purchaseCost / $depreciationLifeMonths;
$bookValue = max(0, $purchaseCost - ($monthlyDepreciation * $monthsSincePurchase));
```

## Customization

### Adding Custom Asset Statuses
Update the `Asset` model:

```php
public static function getStatuses(): array
{
    return [
        'IN_STOCK' => 'In Stock',
        'ASSIGNED' => 'Assigned',
        'MAINTENANCE' => 'Maintenance',
        'RETIRED' => 'Retired',
        'LOST' => 'Lost',
        'CUSTOM_STATUS' => 'Custom Status', // Add here
    ];
}
```

### Custom Depreciation Methods
Extend the depreciation calculation in the `Asset` model's `getBookValueAttribute()` method.

### Additional File Types
Update `AttachmentRequest` validation rules to support more file types.

## Troubleshooting

### Common Issues

1. **Storage symlink not working**
   ```bash
   php artisan storage:link
   ```

2. **QR codes not generating**
   - Check GD extension is installed
   - Verify storage permissions

3. **Import failing**
   - Check file format matches template
   - Verify required relationships exist
   - Check file size limits

4. **API authentication issues**
   - Ensure Sanctum is configured
   - Check token in Authorization header
   - Verify user has required permissions

### Performance Optimization

For large datasets:
- Add database indexes for frequently filtered columns
- Use eager loading for relationships
- Implement caching for lookup data
- Consider pagination limits

## Support

For issues or feature requests, please:
1. Check the troubleshooting section
2. Review the test suite for examples
3. Check Laravel and package documentation
4. Create detailed bug reports with steps to reproduce

## License

This Assets module is part of the Laravel application and follows the same license terms.




