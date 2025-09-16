<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="text-muted mb-0">Client Invoices</h6>
    <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus me-1"></i>New Invoice
    </a>
</div>

<!-- Invoice Status Summary -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="invoice-stat-card stat-paid">
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $invoiceStats['paid'] }}</div>
                <div class="stat-label">Paid</div>
                <div class="stat-amount">${{ number_format($invoiceStats['paid_amount'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="invoice-stat-card stat-pending">
            <div class="stat-icon">
                <i class="bi bi-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $invoiceStats['pending'] }}</div>
                <div class="stat-label">Pending</div>
                <div class="stat-amount">${{ number_format($invoiceStats['pending_amount'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="invoice-stat-card stat-overdue">
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $invoiceStats['overdue'] }}</div>
                <div class="stat-label">Overdue</div>
                <div class="stat-amount">
                    ${{ number_format($client->invoices()->where('status', 'sent')->where('due_date', '<', now())->sum('total_amount'), 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="invoice-stat-card stat-total">
            <div class="stat-icon">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $invoiceStats['total'] }}</div>
                <div class="stat-label">Total</div>
                <div class="stat-amount">${{ number_format($invoiceStats['total_amount'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

@if($client->invoices && $client->invoices->count() > 0)
    <!-- Invoice Filters -->
    <div class="invoice-filters mb-4">
        <div class="btn-group btn-group-sm" role="group">
            <input type="radio" class="btn-check" name="invoiceFilter" id="filter-all" value="all" checked>
            <label class="btn btn-outline-secondary" for="filter-all">All</label>
            
            <input type="radio" class="btn-check" name="invoiceFilter" id="filter-paid" value="paid">
            <label class="btn btn-outline-success" for="filter-paid">Paid</label>
            
            <input type="radio" class="btn-check" name="invoiceFilter" id="filter-pending" value="pending">
            <label class="btn btn-outline-warning" for="filter-pending">Pending</label>
            
            <input type="radio" class="btn-check" name="invoiceFilter" id="filter-overdue" value="overdue">
            <label class="btn btn-outline-danger" for="filter-overdue">Overdue</label>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Invoice #</th>
                    <th>Project</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="invoices-tbody">
                @foreach($client->invoices as $invoice)
                    <tr class="invoice-row" data-status="{{ $invoice->status }}" data-overdue="{{ $invoice->status === 'sent' && $invoice->due_date < now() ? 'true' : 'false' }}">
                        <td>
                            <div class="invoice-number">
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none fw-bold">
                                    {{ $invoice->invoice_number }}
                                </a>
                                @if($invoice->notes)
                                    <div class="invoice-notes">
                                        {{ Str::limit($invoice->notes, 50) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($invoice->project)
                                <a href="{{ route('projects.show', $invoice->project) }}" class="text-decoration-none">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-folder me-1"></i>{{ $invoice->project->name }}
                                    </span>
                                </a>
                            @else
                                <span class="text-muted">No project</span>
                            @endif
                        </td>
                        <td>
                            <div class="invoice-amount">
                                <span class="amount">£{{ number_format($invoice->total_amount, 2) }}</span>
                                @if($invoice->currency && $invoice->currency !== 'GBP')
                                    <small class="currency">{{ $invoice->currency }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'sent' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                            @if($invoice->status === 'sent' && $invoice->due_date < now())
                                <span class="badge bg-danger ms-1">Overdue</span>
                            @endif
                        </td>
                        <td>
                            <div class="date-info">
                                {{ $invoice->issue_date->format('M j, Y') }}
                                <div class="date-relative">{{ $invoice->issue_date->diffForHumans() }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                {{ $invoice->due_date->format('M j, Y') }}
                                <div class="date-relative {{ $invoice->due_date < now() && $invoice->status !== 'paid' ? 'text-danger' : '' }}">
                                    {{ $invoice->due_date->diffForHumans() }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-primary" title="View Invoice">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($invoice->status !== 'paid')
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary" title="Edit Invoice">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                                <button class="btn btn-outline-info" title="Download PDF" onclick="downloadInvoice({{ $invoice->id }})">
                                    <i class="bi bi-download"></i>
                                </button>
                                @if($invoice->status === 'draft')
                                    <button class="btn btn-outline-success" title="Send Invoice" onclick="sendInvoice({{ $invoice->id }})">
                                        <i class="bi bi-send"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($client->invoices->count() > 10)
        <div class="text-center mt-4">
            <a href="{{ route('invoices.index') }}?client_id={{ $client->id }}" class="btn btn-outline-primary">
                View All Invoices
            </a>
        </div>
    @endif
@else
    <div class="empty-state-large">
        <div class="empty-icon">
            <i class="bi bi-receipt"></i>
        </div>
        <h5>No Invoices Yet</h5>
        <p class="text-muted">This client doesn't have any invoices yet. Create the first invoice to get started.</p>
        <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
            <i class="bi bi-plus me-2"></i>Create First Invoice
        </a>
    </div>
@endif

<style>
.invoice-stat-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    height: 100%;
    transition: all 0.2s ease;
}

.invoice-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.stat-paid { border-left: 4px solid #10b981; }
.stat-pending { border-left: 4px solid #f59e0b; }
.stat-overdue { border-left: 4px solid #ef4444; }
.stat-total { border-left: 4px solid #6366f1; }

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-paid .stat-icon { background: #10b981; }
.stat-pending .stat-icon { background: #f59e0b; }
.stat-overdue .stat-icon { background: #ef4444; }
.stat-total .stat-icon { background: #6366f1; }

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.stat-amount {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.invoice-filters {
    display: flex;
    justify-content: center;
}

.invoice-number a {
    color: #1f2937;
}

.invoice-number a:hover {
    color: #4f46e5;
}

.invoice-notes {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.invoice-amount {
    font-weight: 600;
}

.amount {
    color: #059669;
    font-size: 1.1rem;
}

.currency {
    color: #6b7280;
    margin-left: 0.25rem;
}

.date-info {
    font-size: 0.875rem;
}

.date-relative {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.125rem;
}

.empty-state-large {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: #9ca3af;
}

.empty-state-large h5 {
    margin-bottom: 0.5rem;
    color: #374151;
}

.empty-state-large p {
    max-width: 400px;
    margin: 0 auto 2rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.invoice-row {
    transition: opacity 0.2s ease;
}

.invoice-row.hidden {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Invoice filtering
    const filterButtons = document.querySelectorAll('input[name="invoiceFilter"]');
    const invoiceRows = document.querySelectorAll('.invoice-row');

    filterButtons.forEach(button => {
        button.addEventListener('change', function() {
            const filter = this.value;
            
            invoiceRows.forEach(row => {
                const status = row.getAttribute('data-status');
                const isOverdue = row.getAttribute('data-overdue') === 'true';
                
                let show = false;
                
                switch(filter) {
                    case 'all':
                        show = true;
                        break;
                    case 'paid':
                        show = status === 'paid';
                        break;
                    case 'pending':
                        show = status === 'sent' && !isOverdue;
                        break;
                    case 'overdue':
                        show = status === 'sent' && isOverdue;
                        break;
                }
                
                if (show) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });
    });
});

function downloadInvoice(invoiceId) {
    // TODO: Implement invoice download functionality
    alert('Download invoice functionality will be implemented');
}

function sendInvoice(invoiceId) {
    // TODO: Implement send invoice functionality
    if (confirm('Send this invoice to the client?')) {
        alert('Send invoice functionality will be implemented');
    }
}
</script>


