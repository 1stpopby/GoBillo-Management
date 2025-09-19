<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        // Site managers should only see operative invoices
        if (auth()->user()->role === 'site_manager') {
            return $this->operativeInvoicesIndex($request);
        }
        
        // Determine invoice type (client or operative)
        $invoiceType = $request->get('type', 'client'); // default to client invoices
        
        if ($invoiceType === 'operative') {
            return $this->operativeInvoicesIndex($request);
        }
        
        $query = Invoice::forCompany()->with(['client', 'project']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(15);

        // Get filter options
        $clients = Client::forCompany()->orderBy('company_name')->get();
        $projects = Project::forCompany()->orderBy('name')->get();

        return view('invoices.index', compact('invoices', 'clients', 'projects', 'invoiceType'));
    }
    
    /**
     * Display operative invoices for manager approval
     */
    private function operativeInvoicesIndex(Request $request)
    {
        $query = \App\Models\OperativeInvoice::forCompany()->with(['operative', 'manager', 'site', 'project']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('operative', function ($operativeQuery) use ($search) {
                      $operativeQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('operative_id')) {
            $query->where('operative_id', $request->operative_id);
        }

        if ($request->filled('manager_id')) {
            $query->where('manager_id', $request->manager_id);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('date_from')) {
            $query->where('week_starting', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('week_ending', '<=', $request->date_to);
        }

        // If user is a site manager, only show invoices assigned to them
        if (auth()->user()->role === 'site_manager') {
            $query->where('manager_id', auth()->id());
        }

        $operativeInvoices = $query->latest()->paginate(15);

        // Get filter options
        $operatives = \App\Models\User::where('role', 'operative')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        $managers = \App\Models\User::where('role', 'site_manager')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
            
        $sites = \App\Models\Site::forCompany()->orderBy('name')->get();

        return view('invoices.operative-index', compact(
            'operativeInvoices', 
            'operatives', 
            'managers', 
            'sites'
        ));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create()
    {
        $clients = Client::forCompany()->orderBy('company_name')->get();
        $projects = Project::forCompany()->orderBy('name')->get();

        return view('invoices.create', compact('clients', 'projects'));
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $invoice = Invoice::create([
                'company_id' => auth()->user()->company_id,
                'client_id' => $request->client_id,
                'project_id' => $request->project_id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'tax_rate' => $request->tax_rate ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'currency' => $request->currency,
                'notes' => $request->notes,
                'terms' => $request->terms,
            ]);

            foreach ($request->items as $index => $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'sort_order' => $index,
                ]);
            }

            $invoice->calculateTotals();
        });

        return redirect()->route('invoices.index')
                        ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        $invoice->load(['client', 'project', 'items', 'company']);
        
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice
     */
    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->with('error', 'Cannot edit paid invoices.');
        }

        $invoice->load('items');
        $clients = Client::forCompany()->orderBy('company_name')->get();
        $projects = Project::forCompany()->orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'clients', 'projects'));
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->with('error', 'Cannot edit paid invoices.');
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $invoice->update([
                'client_id' => $request->client_id,
                'project_id' => $request->project_id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'tax_rate' => $request->tax_rate ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'currency' => $request->currency,
                'notes' => $request->notes,
                'terms' => $request->terms,
            ]);

            // Delete existing items and recreate
            $invoice->items()->delete();

            foreach ($request->items as $index => $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'sort_order' => $index,
                ]);
            }

            $invoice->calculateTotals();
        });

        return redirect()->route('invoices.show', $invoice)
                        ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        
        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->with('error', 'Cannot delete paid invoices.');
        }

        $invoiceNumber = $invoice->invoice_number;
        $invoice->delete();

        return redirect()->route('invoices.index')
                        ->with('success', "Invoice {$invoiceNumber} deleted successfully.");
    }

    /**
     * Send invoice to client
     */
    public function send(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->with('error', 'Invoice is already paid.');
        }

        $invoice->markAsSent();

        return back()->with('success', 'Invoice sent successfully.');
    }

    /**
     * Mark invoice as paid
     */
    public function markPaid(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        $request->validate([
            'payment_method' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $invoice->markAsPaid($request->payment_method, $request->payment_reference);
        
        return redirect()->route('financial-reports.index')
            ->with('success', 'Invoice marked as paid and reports updated.');
    }

    /**
     * Generate PDF for invoice
     */
    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        $invoice->load(['client', 'project', 'items', 'company']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Duplicate invoice
     */
    public function duplicate(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        $newInvoice = $invoice->replicate();
        $newInvoice->status = Invoice::STATUS_DRAFT;
        $newInvoice->invoice_number = null; // Will be auto-generated
        $newInvoice->sent_at = null;
        $newInvoice->paid_at = null;
        $newInvoice->payment_method = null;
        $newInvoice->payment_reference = null;
        $newInvoice->issue_date = now()->toDateString();
        $newInvoice->due_date = now()->addDays(30)->toDateString();
        $newInvoice->save();

        foreach ($invoice->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $newInvoice->id;
            $newItem->save();
        }

        $newInvoice->calculateTotals();

        return redirect()->route('invoices.edit', $newInvoice)
                        ->with('success', 'Invoice duplicated successfully.');
    }
    
    /**
     * Approve an operative invoice
     */
    public function approveOperativeInvoice(Request $request, $invoiceId)
    {
        $operativeInvoice = \App\Models\OperativeInvoice::findOrFail($invoiceId);
        
        // Check authorization
        if (!auth()->user()->canManageOperativeInvoices() && $operativeInvoice->manager_id !== auth()->id()) {
            abort(403, 'You can only approve invoices assigned to you.');
        }
        
        // Check if invoice is in submitted status
        if ($operativeInvoice->status !== \App\Models\OperativeInvoice::STATUS_SUBMITTED) {
            return back()->with('error', 'Only submitted invoices can be approved.');
        }
        
        $request->validate([
            'approval_notes' => 'nullable|string|max:500'
        ]);
        
        $operativeInvoice->update([
            'status' => \App\Models\OperativeInvoice::STATUS_APPROVED,
            'approved_at' => now(),
            'notes' => $operativeInvoice->notes . ($request->approval_notes ? "\n\nApproval Notes: " . $request->approval_notes : '')
        ]);
        
        return back()->with('success', 'Invoice approved successfully.');
    }
    
    /**
     * Reject an operative invoice
     */
    public function rejectOperativeInvoice(Request $request, $invoiceId)
    {
        $operativeInvoice = \App\Models\OperativeInvoice::findOrFail($invoiceId);
        
        // Check authorization
        if (!auth()->user()->canManageOperativeInvoices() && $operativeInvoice->manager_id !== auth()->id()) {
            abort(403, 'You can only reject invoices assigned to you.');
        }
        
        // Check if invoice is in submitted status
        if ($operativeInvoice->status !== \App\Models\OperativeInvoice::STATUS_SUBMITTED) {
            return back()->with('error', 'Only submitted invoices can be rejected.');
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $operativeInvoice->update([
            'status' => \App\Models\OperativeInvoice::STATUS_REJECTED,
            'notes' => $operativeInvoice->notes . "\n\nRejection Reason: " . $request->rejection_reason
        ]);
        
        return back()->with('success', 'Invoice rejected successfully.');
    }
    
    /**
     * Show operative invoice details
     */
    public function showOperativeInvoice($invoiceId)
    {
        $operativeInvoice = \App\Models\OperativeInvoice::with(['operative', 'manager', 'site', 'project', 'items'])
            ->findOrFail($invoiceId);
        
        // Check authorization
        if (!auth()->user()->canManageOperativeInvoices() && 
            $operativeInvoice->manager_id !== auth()->id() && 
            $operativeInvoice->operative_id !== auth()->id()) {
            abort(403, 'Access denied.');
        }
        
        return view('invoices.operative-show', compact('operativeInvoice'));
    }

    /**
     * Generate PDF for operative invoice
     */
    public function operativeInvoicePdf($invoiceId)
    {
        $operativeInvoice = \App\Models\OperativeInvoice::with(['operative', 'manager', 'site', 'project', 'items', 'company'])
            ->findOrFail($invoiceId);
        
        // Check authorization
        if (!auth()->user()->canManageOperativeInvoices() && 
            $operativeInvoice->manager_id !== auth()->id() && 
            $operativeInvoice->operative_id !== auth()->id()) {
            abort(403, 'Access denied.');
        }

        $pdf = Pdf::loadView('invoices.operative-pdf', compact('operativeInvoice'));
        
        return $pdf->download("operative-invoice-{$operativeInvoice->invoice_number}.pdf");
    }

    /**
     * Display approved operative invoices for company admin to mark as paid
     */
    public function adminOperativeInvoicesIndex(Request $request)
    {
        // Only company admins can access this
        if (auth()->user()->role !== 'company_admin') {
            abort(403, 'Access denied. Only company admins can access this section.');
        }

        $query = \App\Models\OperativeInvoice::with(['operative', 'manager', 'site', 'project'])
            ->forCompany(auth()->user()->company_id)
            ->where('status', 'approved'); // Only show approved invoices awaiting payment

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('operative', function ($operativeQuery) use ($search) {
                    $operativeQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('site', function ($siteQuery) use ($search) {
                    $siteQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('project', function ($projectQuery) use ($search) {
                    $projectQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('operative_id')) {
            $query->where('operative_id', $request->operative_id);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->where('week_starting', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('week_ending', '<=', $request->date_to);
        }

        $operativeInvoices = $query->orderBy('week_starting', 'desc')->paginate(20);

        // Get filter options
        $operatives = \App\Models\User::where('role', 'operative')
            ->where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $sites = \App\Models\Site::forCompany(auth()->user()->company_id)
            ->orderBy('name')
            ->get();

        $projects = \App\Models\Project::forCompany(auth()->user()->company_id)
            ->orderBy('name')
            ->get();

        // Calculate totals
        $totalAmount = $operativeInvoices->sum('gross_amount');
        $totalCisDeduction = $operativeInvoices->sum('cis_deduction');
        $totalNetAmount = $operativeInvoices->sum('net_amount');

        return view('admin.operative-invoices.index', compact(
            'operativeInvoices', 
            'operatives', 
            'sites', 
            'projects', 
            'totalAmount', 
            'totalCisDeduction', 
            'totalNetAmount'
        ));
    }

    /**
     * Mark an approved operative invoice as paid
     */
    public function markOperativeInvoicePaid(Request $request, $invoice)
    {
        // Only company admins can mark invoices as paid
        if (auth()->user()->role !== 'company_admin') {
            abort(403, 'Access denied. Only company admins can mark invoices as paid.');
        }

        $operativeInvoice = \App\Models\OperativeInvoice::forCompany(auth()->user()->company_id)
            ->findOrFail($invoice);

        // Can only mark approved invoices as paid
        if ($operativeInvoice->status !== 'approved') {
            return back()->with('error', 'Only approved invoices can be marked as paid.');
        }

        $operativeInvoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => auth()->id(),
            'notes' => $operativeInvoice->notes . 
                      ($operativeInvoice->notes ? "\n\n" : '') . 
                      "Admin Payment Notes (" . now()->format('M j, Y g:i A') . "): " . 
                      ($request->input('admin_notes') ?: 'Approved and marked as paid.')
        ]);

        return back()->with('success', 'Invoice marked as paid successfully.');
    }
} 