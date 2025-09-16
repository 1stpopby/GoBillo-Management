<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Project Invoices</h5>
    <div class="btn-group btn-group-sm" role="group">
        <input type="radio" class="btn-check" name="invoiceFilter" id="all-invoices" autocomplete="off" checked>
        <label class="btn btn-outline-primary" for="all-invoices">All</label>

        <input type="radio" class="btn-check" name="invoiceFilter" id="pending-invoices" autocomplete="off">
        <label class="btn btn-outline-warning" for="pending-invoices">Pending</label>

        <input type="radio" class="btn-check" name="invoiceFilter" id="paid-invoices" autocomplete="off">
        <label class="btn btn-outline-success" for="paid-invoices">Paid</label>
    </div>
</div>

@if($invoices->count() > 0)
    <div class="row" id="invoicesContainer">
        @foreach($invoices as $invoice)
            <div class="col-lg-6 mb-3 invoice-item" data-status="{{ $invoice->status }}">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none">
                                {{ $invoice->invoice_number }}
                            </a>
                        </h6>
                        <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'sent' ? 'warning' : ($invoice->status === 'overdue' ? 'danger' : 'secondary')) }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="detail-group">
                                    <label class="detail-label">Client</label>
                                    <div class="detail-value">
                                        <a href="{{ route('clients.show', $invoice->client) }}" class="text-decoration-none">
                                            {{ $invoice->client->name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-group">
                                    <label class="detail-label">Amount</label>
                                    <div class="detail-value fw-bold">
                                        ${{ number_format($invoice->total_amount, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-group">
                                    <label class="detail-label">Issue Date</label>
                                    <div class="detail-value">{{ $invoice->issue_date->format('M j, Y') }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-group">
                                    <label class="detail-label">Due Date</label>
                                    <div class="detail-value">
                                        {{ $invoice->due_date->format('M j, Y') }}
                                        @if($invoice->status !== 'paid' && $invoice->due_date->isPast())
                                            <span class="badge bg-danger ms-1">Overdue</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($invoice->project)
                                <div class="col-12">
                                    <div class="detail-group">
                                        <label class="detail-label">Project</label>
                                        <div class="detail-value">
                                            <a href="{{ route('projects.show', $invoice->project) }}" class="text-decoration-none">
                                                {{ $invoice->project->name }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Payment Information -->
                        @if($invoice->status === 'paid' && $invoice->paid_at)
                            <div class="mt-3 pt-3 border-top">
                                <div class="small text-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Paid on {{ $invoice->paid_at->format('M j, Y') }}
                                    @if($invoice->payment_method)
                                        via {{ $invoice->payment_method }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Created {{ $invoice->created_at->diffForHumans() }}
                            </small>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->canManageProjects())
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Invoice Summary -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">Invoice Summary</h6>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-primary">{{ $invoices->count() }}</div>
                        <div class="text-muted small">Total Invoices</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-success">
                            ${{ number_format($invoices->where('status', 'paid')->sum('total_amount'), 0) }}
                        </div>
                        <div class="text-muted small">Paid Amount</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-warning">
                            ${{ number_format($invoices->whereIn('status', ['sent', 'overdue'])->sum('total_amount'), 0) }}
                        </div>
                        <div class="text-muted small">Outstanding</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-danger">
                            {{ $invoices->where('status', 'overdue')->count() }}
                        </div>
                        <div class="text-muted small">Overdue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-primary">
            View All Invoices
        </a>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-receipt display-1 text-muted"></i>
        <h5 class="mt-3">No Invoices</h5>
        <p class="text-muted">This employee is not associated with any project invoices yet.</p>
        @if(auth()->user()->canManageProjects())
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create Invoice
            </a>
        @endif
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('input[name="invoiceFilter"]');
    const invoiceItems = document.querySelectorAll('.invoice-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('change', function() {
            const filter = this.id.replace('-invoices', '');
            
            invoiceItems.forEach(item => {
                const status = item.dataset.status;
                
                if (filter === 'all') {
                    item.style.display = 'block';
                } else if (filter === 'pending') {
                    item.style.display = ['sent', 'overdue'].includes(status) ? 'block' : 'none';
                } else if (filter === 'paid') {
                    item.style.display = status === 'paid' ? 'block' : 'none';
                } else {
                    item.style.display = status === filter ? 'block' : 'none';
                }
            });
        });
    });
});
</script>


