@extends('layouts.app')

@section('title', 'Edit Site - ' . $site->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sites.show', $site) }}">{{ $site->name }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                <h1 class="page-title">Edit Site</h1>
                <p class="page-subtitle">Update site information and settings</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Site Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sites.update', $site) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Site Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $site->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                <select class="form-select @error('client_id') is-invalid @enderror" 
                                        id="client_id" name="client_id" required>
                                    <option value="">Select a client...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $site->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description', $site->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location Information -->
                            <div class="col-12">
                                <h6 class="text-muted mb-3 mt-3">Location Information</h6>
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address', $site->address) }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city', $site->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       id="state" name="state" value="{{ old('state', $site->state) }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="zip_code" class="form-label">ZIP Code</label>
                                <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                       id="zip_code" name="zip_code" value="{{ old('zip_code', $site->zip_code) }}">
                                @error('zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project Details -->
                            <div class="col-12">
                                <h6 class="text-muted mb-3 mt-3">Project Details</h6>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="planning" {{ old('status', $site->status) == 'planning' ? 'selected' : '' }}>Planning</option>
                                    <option value="active" {{ old('status', $site->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="on_hold" {{ old('status', $site->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="completed" {{ old('status', $site->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $site->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" required>
                                    <option value="low" {{ old('priority', $site->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $site->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $site->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority', $site->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="total_budget" class="form-label">Total Budget</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control @error('total_budget') is-invalid @enderror" 
                                           id="total_budget" name="total_budget" value="{{ old('total_budget', $site->total_budget) }}" 
                                           min="0" step="0.01">
                                    @error('total_budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Empty column for spacing -->
                            </div>

                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', $site->start_date?->format('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="expected_completion_date" class="form-label">Expected Completion Date</label>
                                <input type="date" class="form-control @error('expected_completion_date') is-invalid @enderror" 
                                       id="expected_completion_date" name="expected_completion_date" 
                                       value="{{ old('expected_completion_date', $site->expected_completion_date?->format('Y-m-d')) }}">
                                @error('expected_completion_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Site Management -->
                            <div class="col-12">
                                <h6 class="text-muted mb-3 mt-3">Site Management</h6>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Site Managers</label>
                                <div class="card">
                                    <div class="card-body">
                                        <div id="managers-container">
                                            @php
                                                $currentManagers = $site->activeManagers;
                                            @endphp
                                            
                                            @if($currentManagers->count() > 0)
                                                @foreach($currentManagers as $index => $manager)
                                                    <div class="manager-row mb-3">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-8">
                                                                <select class="form-select manager-select" name="managers[]">
                                                                    <option value="">Select manager...</option>
                                                                    @foreach($managers as $availableManager)
                                                                        <option value="{{ $availableManager->id }}" 
                                                                                data-contact="{{ $availableManager->name }}" 
                                                                                data-phone="{{ $availableManager->phone }}"
                                                                                {{ $manager->id == $availableManager->id ? 'selected' : '' }}>
                                                                            {{ $availableManager->name }} ({{ $availableManager->email }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <span class="badge {{ $manager->pivot->role === 'primary' ? 'bg-primary' : 'bg-secondary' }}">
                                                                    {{ ucfirst($manager->pivot->role) }}
                                                                </span>
                                                            </div>
                                                            <div class="col-md-2">
                                                                @if($manager->pivot->role !== 'primary')
                                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-manager">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="manager-row mb-3">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-8">
                                                            <select class="form-select manager-select" name="managers[]">
                                                                <option value="">Select primary manager...</option>
                                                                @foreach($managers as $manager)
                                                                    <option value="{{ $manager->id }}" data-contact="{{ $manager->name }}" data-phone="{{ $manager->phone }}">
                                                                        {{ $manager->name }} ({{ $manager->email }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span class="badge bg-primary">Primary</span>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <!-- Cannot remove primary manager -->
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-manager">
                                            <i class="bi bi-plus-circle me-2"></i>Add Additional Manager
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="site_manager_contact" class="form-label">Site Manager Contact</label>
                                <input type="text" class="form-control @error('site_manager_contact') is-invalid @enderror" 
                                       id="site_manager_contact" name="site_manager_contact" 
                                       value="{{ old('site_manager_contact', $site->site_manager_contact) }}">
                                @error('site_manager_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="site_manager_phone" class="form-label">Site Manager Phone</label>
                                <input type="tel" class="form-control @error('site_manager_phone') is-invalid @enderror" 
                                       id="site_manager_phone" name="site_manager_phone" 
                                       value="{{ old('site_manager_phone', $site->site_manager_phone) }}">
                                @error('site_manager_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3">{{ old('notes', $site->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-between">
                                    <div>
                                        @if(auth()->user()->canManageProjects())
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteSiteModal">
                                                <i class="bi bi-trash me-2"></i>Delete Site
                                            </button>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('sites.show', $site) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>Update Site
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(auth()->user()->canManageProjects())
<div class="modal fade" id="deleteSiteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Site</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $site->name }}</strong>?</p>
                <p class="text-muted">This action cannot be undone. All projects and tasks associated with this site will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('sites.destroy', $site) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Site</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const managersContainer = document.getElementById('managers-container');
  const addManagerBtn = document.getElementById('add-manager');
  const contactInput = document.getElementById('site_manager_contact');
  const phoneInput = document.getElementById('site_manager_phone');
  
  let managerCount = document.querySelectorAll('.manager-row').length;

  // Available managers template
  const managersOptions = `
    <option value="">Select manager...</option>
    @foreach($managers as $manager)
      <option value="{{ $manager->id }}" data-contact="{{ $manager->name }}" data-phone="{{ $manager->phone }}">
        {{ $manager->name }} ({{ $manager->email }})
      </option>
    @endforeach
  `;

  // Function to sync manager details from primary manager
  function syncManagerDetails() {
    const primarySelect = document.querySelector('.manager-select');
    if (primarySelect) {
      const opt = primarySelect.options[primarySelect.selectedIndex];
      if (opt && opt.dataset) {
        if (!contactInput.value) contactInput.value = opt.dataset.contact || '';
        if (!phoneInput.value) phoneInput.value = opt.dataset.phone || '';
      }
    }
  }

  // Function to update selected managers (prevent duplicates)
  function updateManagerOptions() {
    const selectedManagers = Array.from(document.querySelectorAll('.manager-select'))
      .map(select => select.value)
      .filter(value => value !== '');

    document.querySelectorAll('.manager-select').forEach(select => {
      const currentValue = select.value;
      Array.from(select.options).forEach(option => {
        if (option.value === '') return; // Keep empty option
        
        // Disable if selected elsewhere, but keep current selection enabled
        option.disabled = selectedManagers.includes(option.value) && option.value !== currentValue;
      });
    });
  }

  // Add new manager row
  addManagerBtn.addEventListener('click', function() {
    managerCount++;
    const newRow = document.createElement('div');
    newRow.className = 'manager-row mb-3';
    newRow.innerHTML = `
      <div class="row align-items-center">
        <div class="col-md-8">
          <select class="form-select manager-select" name="managers[]">
            ${managersOptions}
          </select>
        </div>
        <div class="col-md-2">
          <span class="badge bg-secondary">Secondary</span>
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-outline-danger btn-sm remove-manager">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    `;
    
    managersContainer.appendChild(newRow);
    
    // Add event listeners to new elements
    const newSelect = newRow.querySelector('.manager-select');
    const removeBtn = newRow.querySelector('.remove-manager');
    
    newSelect.addEventListener('change', updateManagerOptions);
    removeBtn.addEventListener('click', function() {
      newRow.remove();
      updateManagerOptions();
    });
    
    updateManagerOptions();
  });

  // Add event listeners to existing remove buttons
  document.querySelectorAll('.remove-manager').forEach(btn => {
    btn.addEventListener('click', function() {
      btn.closest('.manager-row').remove();
      updateManagerOptions();
    });
  });

  // Add event listeners to existing manager selects
  document.querySelectorAll('.manager-select').forEach(select => {
    select.addEventListener('change', function() {
      syncManagerDetails();
      updateManagerOptions();
    });
  });

  // Initial setup
  syncManagerDetails();
  updateManagerOptions();
});
</script>
@endpush
@endsection