@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
                <li class="breadcrumb-item"><a href="{{ route('clients.show', $client) }}">{{ $client->name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Edit Client</h1>
    </div>
    <div class="btn-group">
        <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-info">
            <i class="bi bi-eye"></i> View Client
        </a>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Clients
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Client Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('clients.update', $client) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $client->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $client->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $client->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" class="form-control @error('company') is-invalid @enderror" 
                                   id="company" name="company" value="{{ old('company', $client->company) }}">
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                               id="address" name="address" value="{{ old('address', $client->address) }}" 
                               placeholder="Street address">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" value="{{ old('city', $client->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                   id="state" name="state" value="{{ old('state', $client->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="zip_code" class="form-label">ZIP Code</label>
                            <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                   id="zip_code" name="zip_code" value="{{ old('zip_code', $client->zip_code) }}">
                            @error('zip_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="4" 
                                  placeholder="Any additional notes about the client...">{{ old('notes', $client->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                   type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $client->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Client
                            </label>
                            <div class="form-text">Inactive clients won't appear in project assignments</div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            @if(auth()->user()->canManageClients())
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash"></i> Delete Client
                                </button>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Client
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Client Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Client Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary mb-0">{{ $client->active_projects_count }}</h4>
                        <small class="text-muted">Active Projects</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">{{ $client->completed_projects_count }}</h4>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-info mb-0">${{ number_format($client->projects->sum('budget'), 0) }}</h4>
                        <small class="text-muted">Total Budget</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning mb-0">{{ $client->projects->count() }}</h4>
                        <small class="text-muted">Total Projects</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        @if($client->projects->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Recent Projects</h6>
                </div>
                <div class="card-body">
                    @foreach($client->projects->take(3) as $project)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                    <strong>{{ Str::limit($project->name, 20) }}</strong>
                                </a>
                                <br>
                                <small class="text-muted">{{ $project->created_at->format('M j, Y') }}</small>
                            </div>
                            <span class="badge bg-{{ $project->status_color }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </div>
                        @if(!$loop->last)<hr class="my-2">@endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tips -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2">
                        <i class="bi bi-info-circle text-info"></i>
                        <strong>Email Changes:</strong> Changing the email will affect project notifications.
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        <strong>Active Status:</strong> Deactivating will hide this client from new project assignments.
                    </li>
                    <li>
                        <i class="bi bi-shield-check text-success"></i>
                        <strong>Data Safety:</strong> Client information is securely stored and encrypted.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(auth()->user()->canManageClients())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this client? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> This will permanently remove the client and all associated data.
                </div>
                @if($client->projects->count() > 0)
                    <div class="alert alert-danger">
                        <strong>Cannot Delete:</strong> This client has {{ $client->projects->count() }} project(s). 
                        Please remove or transfer all projects before deleting the client.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @if($client->projects->count() == 0)
                    <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Client</button>
                    </form>
                @else
                    <button type="button" class="btn btn-danger" disabled>Cannot Delete</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<script>
function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection 