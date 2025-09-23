<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Client;
use App\Models\Project;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['invoice.client', 'invoice.project', 'paymentMethod'])
            ->forCompany();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhereHas('invoice.client', function ($clientQuery) use ($search) {
                      $clientQuery->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->whereHas('invoice', function ($invoiceQuery) use ($request) {
                $invoiceQuery->where('client_id', $request->client_id);
            });
        }

        if ($request->filled('project_id')) {
            $query->whereHas('invoice', function ($invoiceQuery) use ($request) {
                $invoiceQuery->where('project_id', $request->project_id);
            });
        }

        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        if ($request->filled('date_from')) {
            $query->where('processed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('processed_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(15);

        // Get filter options
        $clients = Client::forCompany()->orderBy('company_name')->get();
        $projects = Project::forCompany()->orderBy('name')->get();
        $paymentMethods = PaymentMethod::forCompany()->active()->orderBy('name')->get();

        // Calculate statistics using separate queries to avoid mutation
        $baseQuery = Payment::forCompany();
        $stats = [
            'total_payments' => (clone $baseQuery)->count(),
            'total_amount' => (clone $baseQuery)->sum('amount'),
            'completed_amount' => (clone $baseQuery)->where('status', Payment::STATUS_COMPLETED)->sum('amount'),
            'pending_amount' => (clone $baseQuery)->where('status', Payment::STATUS_PENDING)->sum('amount'),
        ];

        return view('payments.index', compact('payments', 'clients', 'projects', 'paymentMethods', 'stats'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(Request $request)
    {
        // Get filter options
        $clients = Client::forCompany()->orderBy('company_name')->get();
        $projects = Project::forCompany()->orderBy('name')->get();
        $sites = Site::forCompany()->orderBy('name')->get();
        $paymentMethods = PaymentMethod::forCompany()->active()->orderBy('name')->get();
        
        // Get unpaid invoices
        $unpaidInvoices = Invoice::forCompany()
            ->whereIn('status', ['sent', 'overdue', 'partial'])
            ->with(['client', 'project'])
            ->orderBy('due_date')
            ->get();

        // Pre-select invoice if provided
        $selectedInvoice = null;
        if ($request->filled('invoice_id')) {
            $selectedInvoice = Invoice::forCompany()->findOrFail($request->invoice_id);
        }

        return view('payments.create', compact('clients', 'projects', 'sites', 'paymentMethods', 'unpaidInvoices', 'selectedInvoice'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:full_payment,partial_payment,deposit',
            'payment_gateway' => 'required|in:manual,bank_transfer,cash,cheque,stripe,paypal,square',
            'provider_transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,failed',
        ]);

        // Validate payment method belongs to company if provided
        if ($validated['payment_method_id']) {
            $paymentMethod = PaymentMethod::forCompany()->active()->find($validated['payment_method_id']);
            if (!$paymentMethod) {
                return back()->withErrors(['payment_method_id' => 'Invalid payment method selected.']);
            }
        }

        // Get the invoice
        $invoice = Invoice::forCompany()->findOrFail($validated['invoice_id']);

        // Validate payment amount doesn't exceed remaining balance
        $remainingBalance = $invoice->total - $invoice->payments()->completed()->sum('amount');
        if ($validated['amount'] > $remainingBalance) {
            return back()->withErrors(['amount' => 'Payment amount cannot exceed remaining invoice balance of £' . number_format($remainingBalance, 2)]);
        }

        DB::transaction(function () use ($validated, $invoice) {
            // Calculate processing fee if payment method is selected
            $processingFee = 0;
            if ($validated['payment_method_id']) {
                $paymentMethod = PaymentMethod::forCompany()->active()->findOrFail($validated['payment_method_id']);
                $processingFee = $paymentMethod->calculateProcessingFee($validated['amount']);
            }

            // Create payment
            $payment = Payment::create([
                'company_id' => auth()->user()->company_id,
                'invoice_id' => $validated['invoice_id'],
                'payment_method_id' => $validated['payment_method_id'],
                'payment_number' => Payment::generatePaymentNumber(),
                'status' => $validated['status'],
                'amount' => $validated['amount'],
                'processing_fee' => $processingFee,
                'net_amount' => $validated['amount'] - $processingFee,
                'currency' => 'GBP',
                'payment_type' => $validated['payment_type'],
                'provider_transaction_id' => $validated['provider_transaction_id'],
                'payment_gateway' => $validated['payment_gateway'],
                'notes' => $validated['notes'],
                'processed_at' => $validated['status'] === Payment::STATUS_COMPLETED ? now() : null,
            ]);

            // Update invoice status if payment completes it
            if ($validated['status'] === Payment::STATUS_COMPLETED) {
                $totalPaid = $invoice->payments()->completed()->sum('amount');
                
                if ($totalPaid >= $invoice->total) {
                    $invoice->update(['status' => 'paid']);
                } elseif ($totalPaid > 0) {
                    $invoice->update(['status' => 'partial']);
                }
            }
        });

        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        // Ensure payment belongs to user's company
        if ($payment->company_id !== auth()->user()->company_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $payment->load(['invoice.client', 'invoice.project', 'paymentMethod']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit(Payment $payment)
    {
        // Ensure payment belongs to user's company
        if ($payment->company_id !== auth()->user()->company_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        // Only allow editing of pending or failed payments
        if (!in_array($payment->status, [Payment::STATUS_PENDING, Payment::STATUS_FAILED])) {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Only pending or failed payments can be edited.');
        }

        $paymentMethods = PaymentMethod::forCompany()->active()->orderBy('name')->get();
        $payment->load(['invoice.client', 'invoice.project']);

        return view('payments.edit', compact('payment', 'paymentMethods'));
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, Payment $payment)
    {
        // Ensure payment belongs to user's company
        if ($payment->company_id !== auth()->user()->company_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        // Only allow editing of pending or failed payments
        if (!in_array($payment->status, [Payment::STATUS_PENDING, Payment::STATUS_FAILED])) {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Only pending or failed payments can be edited.');
        }

        $validated = $request->validate([
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:full_payment,partial_payment,deposit',
            'payment_gateway' => 'required|in:manual,bank_transfer,cash,cheque,stripe,paypal,square',
            'provider_transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,failed',
        ]);

        // Validate payment method belongs to company if provided
        if (!empty($validated['payment_method_id'])) {
            $paymentMethod = PaymentMethod::forCompany()->active()->find($validated['payment_method_id']);
            if (!$paymentMethod) {
                return back()->withErrors(['payment_method_id' => 'Invalid payment method selected.']);
            }
        }

        // Validate payment amount doesn't exceed remaining balance (excluding this payment)
        $remainingBalance = $payment->invoice->total - $payment->invoice->payments()
            ->completed()
            ->where('id', '!=', $payment->id)
            ->sum('amount');
        
        if ($validated['amount'] > $remainingBalance) {
            return back()->withErrors(['amount' => 'Payment amount cannot exceed remaining invoice balance of £' . number_format($remainingBalance, 2)]);
        }

        DB::transaction(function () use ($validated, $payment) {
            // Calculate processing fee if payment method is selected
            $processingFee = 0;
            $paymentMethodId = $validated['payment_method_id'] ?? null;
            if ($paymentMethodId) {
                $paymentMethod = PaymentMethod::forCompany()->active()->findOrFail($paymentMethodId);
                $processingFee = $paymentMethod->calculateProcessingFee($validated['amount']);
            }

            // Update payment
            $payment->update([
                'payment_method_id' => $paymentMethodId,
                'amount' => $validated['amount'],
                'processing_fee' => $processingFee,
                'net_amount' => $validated['amount'] - $processingFee,
                'payment_type' => $validated['payment_type'],
                'provider_transaction_id' => $validated['provider_transaction_id'],
                'payment_gateway' => $validated['payment_gateway'],
                'notes' => $validated['notes'],
                'status' => $validated['status'],
                'processed_at' => $validated['status'] === Payment::STATUS_COMPLETED ? now() : null,
                'failed_at' => $validated['status'] === Payment::STATUS_FAILED ? now() : null,
            ]);

            // Update invoice status
            $invoice = $payment->invoice;
            $totalPaid = $invoice->payments()->completed()->sum('amount');
            
            if ($totalPaid >= $invoice->total) {
                $invoice->update(['status' => 'paid']);
            } elseif ($totalPaid > 0) {
                $invoice->update(['status' => 'partial']);
            } else {
                $invoice->update(['status' => 'sent']);
            }
        });

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment
     */
    public function destroy(Payment $payment)
    {
        // Ensure payment belongs to user's company
        if ($payment->company_id !== auth()->user()->company_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        // Only allow deletion of pending or failed payments
        if (!in_array($payment->status, [Payment::STATUS_PENDING, Payment::STATUS_FAILED])) {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Only pending or failed payments can be deleted.');
        }

        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}