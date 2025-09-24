@extends('layouts.app')

@section('title', 'Edit Variation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header with Status Badge -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <h1 class="h3 mb-0">Edit Variation {{ $variation->variation_number }}</h1>
                        <span class="badge bg-{{ $variation->status_color }} fs-6 px-3 py-2">
                            <i class="bi bi-{{ $variation->status === 'approved' ? 'check-circle' : ($variation->status === 'rejected' ? 'x-circle' : 'clock') }} me-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $variation->status)) }}
                        </span>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('project.variations.show', ['project' => $project, 'variation' => $variation]) }}">Variation Details</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Quick Cost Update for Admins (when cost_impact is 0) -->
            @if(auth()->user()->role === 'company_admin' && $variation->cost_impact == 0)
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">Cost Agreement Needed</h5>
                        <p class="mb-2">This variation was submitted by a manager without a cost. Please agree the cost with the client and update it below.</p>
                        <form action="{{ route('project.variations.quickCostUpdate', ['project' => $project, 'variation' => $variation]) }}" method="POST" class="d-flex gap-2 align-items-end">
                            @csrf
                            @method('PATCH')
                            <div class="form-group mb-0">
                                <label for="quick_cost" class="form-label small">Cost Impact (£)</label>
                                <input type="number" step="0.01" name="cost_impact" id="quick_cost" class="form-control" 
                                    placeholder="Enter agreed cost" required autofocus>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-currency-pound me-1"></i>Update Cost
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Main Edit Form -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Variation Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('project.variations.update', ['project' => $project, 'variation' => $variation]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                        id="title" name="title" value="{{ old('title', $variation->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Type -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        @foreach($types as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $variation->type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Description -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                        id="description" name="description" rows="4" required>{{ old('description', $variation->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Reason -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="reason" class="form-label">Reason for Change <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('reason') is-invalid @enderror" 
                                        id="reason" name="reason" rows="3" required>{{ old('reason', $variation->reason) }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Cost Impact -->
                            @if(auth()->user()->role === 'company_admin')
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cost_impact" class="form-label">Cost Impact (£) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('cost_impact') is-invalid @enderror" 
                                            id="cost_impact" name="cost_impact" value="{{ old('cost_impact', $variation->cost_impact) }}" required>
                                        <small class="form-text text-muted">Positive for additions, negative for deductions</small>
                                        @error('cost_impact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @else
                                <!-- Hidden field for managers -->
                                <input type="hidden" name="cost_impact" value="{{ $variation->cost_impact }}">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Cost Impact</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-info">Cost will be agreed with admin after submission</span>
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Time Impact -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="time_impact_days" class="form-label">Time Impact (Days) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('time_impact_days') is-invalid @enderror" 
                                        id="time_impact_days" name="time_impact_days" value="{{ old('time_impact_days', $variation->time_impact_days) }}" required>
                                    <small class="form-text text-muted">Positive for delays, negative for acceleration</small>
                                    @error('time_impact_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Client Reference -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="client_reference" class="form-label">Client Reference</label>
                                    <input type="text" class="form-control @error('client_reference') is-invalid @enderror" 
                                        id="client_reference" name="client_reference" value="{{ old('client_reference', $variation->client_reference) }}">
                                    @error('client_reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Requested Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="requested_date" class="form-label">Requested Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('requested_date') is-invalid @enderror" 
                                        id="requested_date" name="requested_date" 
                                        value="{{ old('requested_date', $variation->requested_date->format('Y-m-d')) }}" required>
                                    @error('requested_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Required By Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="required_by_date" class="form-label">Required By Date</label>
                                    <input type="date" class="form-control @error('required_by_date') is-invalid @enderror" 
                                        id="required_by_date" name="required_by_date" 
                                        value="{{ old('required_by_date', $variation->required_by_date ? $variation->required_by_date->format('Y-m-d') : '') }}">
                                    @error('required_by_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Affected Tasks -->
                        @if($tasks->count() > 0)
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="affected_tasks" class="form-label">Affected Tasks</label>
                                        <select class="form-select @error('affected_tasks') is-invalid @enderror" 
                                            id="affected_tasks" name="affected_tasks[]" multiple>
                                            @foreach($tasks as $task)
                                                <option value="{{ $task->id }}" 
                                                    {{ in_array($task->id, old('affected_tasks', $variation->affected_tasks ?? [])) ? 'selected' : '' }}>
                                                    {{ $task->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple tasks</small>
                                        @error('affected_tasks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('project.variations.show', ['project' => $project, 'variation' => $variation]) }}" 
                                class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize select2 for affected tasks if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus on quick cost input if present
        const quickCostInput = document.getElementById('quick_cost');
        if (quickCostInput) {
            quickCostInput.focus();
        }
    });
</script>
@endsection