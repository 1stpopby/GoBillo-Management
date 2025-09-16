@extends('layouts.app')

@section('title', 'Import Assets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
                <li class="breadcrumb-item active">Import Assets</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Import Assets</h1>
        <p class="text-muted">Bulk import assets from Excel/CSV file</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Upload File</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="file" class="form-label">Select File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" 
                               id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Supported formats: Excel (.xlsx, .xls) and CSV (.csv)</div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing" value="1">
                            <label class="form-check-label" for="update_existing">
                                Update existing assets (match by Asset Code)
                            </label>
                        </div>
                        <div class="form-text">If unchecked, duplicate asset codes will be skipped</div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="validate_only" name="validate_only" value="1">
                            <label class="form-check-label" for="validate_only">
                                Validate only (don't import)
                            </label>
                        </div>
                        <div class="form-text">Check for errors without importing the data</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>Import Assets
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(session('import_results'))
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Import Results</h5>
                </div>
                <div class="card-body">
                    @php $results = session('import_results') @endphp
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="bg-success text-white p-3 rounded text-center">
                                <h4 class="mb-0">{{ $results['imported'] ?? 0 }}</h4>
                                <small>Imported</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-info text-white p-3 rounded text-center">
                                <h4 class="mb-0">{{ $results['updated'] ?? 0 }}</h4>
                                <small>Updated</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-warning text-white p-3 rounded text-center">
                                <h4 class="mb-0">{{ $results['skipped'] ?? 0 }}</h4>
                                <small>Skipped</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-danger text-white p-3 rounded text-center">
                                <h4 class="mb-0">{{ $results['errors'] ?? 0 }}</h4>
                                <small>Errors</small>
                            </div>
                        </div>
                    </div>

                    @if(!empty($results['error_details']))
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">Import Errors:</h6>
                            <ul class="mb-0">
                                @foreach($results['error_details'] as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($results['warnings']))
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Warnings:</h6>
                            <ul class="mb-0">
                                @foreach($results['warnings'] as $warning)
                                    <li>{{ $warning }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Download Template -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Import Template</h6>
            </div>
            <div class="card-body">
                <p class="text-muted">Download the template file to ensure your data is formatted correctly.</p>
                <a href="{{ route('assets.template') }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-download me-1"></i>Download Template
                </a>
            </div>
        </div>

        <!-- Import Instructions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Import Instructions</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Download the import template</li>
                    <li class="mb-2">Fill in your asset data following the column headers</li>
                    <li class="mb-2">Save the file as Excel or CSV format</li>
                    <li class="mb-2">Upload the file using the form</li>
                    <li>Review the import results</li>
                </ol>
            </div>
        </div>

        <!-- Required Fields -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Required Fields</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Asset Code</strong> - Unique identifier
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Name</strong> - Asset name/title
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Status</strong> - Asset status
                    </li>
                </ul>
            </div>
        </div>

        <!-- Optional Fields -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Optional Fields</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Description</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Category</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Location</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Vendor</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Serial Number</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Model Number</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Purchase Date</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Purchase Cost</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Department</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Assigned To</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Warranty Expiry</li>
                    <li class="mb-1"><i class="bi bi-dash me-2"></i>Notes</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// File validation
document.getElementById('file').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                           'application/vnd.ms-excel', 
                           'text/csv'];
        
        if (!validTypes.includes(file.type)) {
            alert('Please select a valid Excel (.xlsx, .xls) or CSV (.csv) file.');
            this.value = '';
            return;
        }

        // Check file size (max 10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB.');
            this.value = '';
            return;
        }

        console.log('File selected:', file.name, 'Size:', (file.size / 1024 / 1024).toFixed(2) + 'MB');
    }
});

// Form submission loading state
document.querySelector('form').addEventListener('submit', function() {
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Processing...';
    
    // Re-enable button after 30 seconds as fallback
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 30000);
});
</script>
@endpush
@endsection


