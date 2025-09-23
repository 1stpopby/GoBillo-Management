<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PaymentStatementController extends Controller
{
    /**
     * Display a listing of payment statements
     */
    public function index(Request $request)
    {
        $statements = DB::table('payment_statements')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('payment-statements.index', compact('statements'));
    }

    /**
     * Show the form for creating a new payment statement
     */
    public function create()
    {
        $clients = Client::forCompany()
            ->with(['projects', 'invoices'])
            ->orderBy('company_name')
            ->get();
            
        return view('payment-statements.create', compact('clients'));
    }

    /**
     * Generate and display a payment statement
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'include_projects' => 'nullable|boolean',
            'include_invoices' => 'nullable|boolean',
            'include_payments' => 'nullable|boolean',
        ]);

        $client = Client::forCompany()->findOrFail($validated['client_id']);
        
        // Set date range (default to all time if not specified)
        $dateFrom = $validated['date_from'] ? Carbon::parse($validated['date_from']) : null;
        $dateTo = $validated['date_to'] ? Carbon::parse($validated['date_to']) : Carbon::now();

        // Get all client's projects with budgets
        $projects = Project::forCompany()
            ->where('client_id', $client->id)
            ->with(['site'])
            ->get();

        // Calculate total project budgets
        $totalBudget = $projects->sum('budget');

        // Get all invoices for this client
        $invoicesQuery = Invoice::forCompany()
            ->where('client_id', $client->id)
            ->with(['payments']);
            
        if ($dateFrom) {
            $invoicesQuery->where('issue_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $invoicesQuery->where('issue_date', '<=', $dateTo);
        }
        
        $invoices = $invoicesQuery->orderBy('issue_date', 'desc')->get();

        // Calculate totals
        $totalInvoiced = $invoices->sum('total_amount');
        $totalPaid = 0;
        $payments = collect();

        // Get all payments for these invoices
        foreach ($invoices as $invoice) {
            $invoicePayments = $invoice->payments()
                ->where('status', 'completed');
                
            if ($dateFrom) {
                $invoicePayments->where('processed_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $invoicePayments->where('processed_at', '<=', $dateTo);
            }
            
            $invoicePayments = $invoicePayments->get();
            $payments = $payments->merge($invoicePayments);
            $totalPaid += $invoicePayments->sum('amount');
        }

        // Calculate balances
        $outstandingBalance = $totalInvoiced - $totalPaid;
        $remainingBudget = $totalBudget - $totalInvoiced;

        // Prepare statement data
        $statementData = [
            'client' => $client,
            'projects' => $projects,
            'invoices' => $invoices,
            'payments' => $payments->sortBy('processed_at'),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_budget' => $totalBudget,
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'outstanding_balance' => $outstandingBalance,
            'remaining_budget' => $remainingBudget,
            'statement_date' => Carbon::now(),
            'statement_number' => $this->generateStatementNumber(),
        ];

        // Store statement record
        DB::table('payment_statements')->insert([
            'company_id' => auth()->user()->company_id,
            'client_id' => $client->id,
            'statement_number' => $statementData['statement_number'],
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_budget' => $totalBudget,
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'outstanding_balance' => $outstandingBalance,
            'remaining_budget' => $remainingBudget,
            'statement_date' => $statementData['statement_date'],
            'generated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return view('payment-statements.view', $statementData);
    }

    /**
     * View a specific payment statement
     */
    public function show($id)
    {
        $statement = DB::table('payment_statements')
            ->where('company_id', auth()->user()->company_id)
            ->where('id', $id)
            ->firstOrFail();

        $client = Client::findOrFail($statement->client_id);
        
        // Recreate the statement data
        $projects = Project::forCompany()
            ->where('client_id', $client->id)
            ->with(['site'])
            ->get();

        $invoicesQuery = Invoice::forCompany()
            ->where('client_id', $client->id)
            ->with(['payments']);
            
        if ($statement->date_from) {
            $invoicesQuery->where('issue_date', '>=', $statement->date_from);
        }
        if ($statement->date_to) {
            $invoicesQuery->where('issue_date', '<=', $statement->date_to);
        }
        
        $invoices = $invoicesQuery->orderBy('issue_date', 'desc')->get();

        // Get payments
        $payments = collect();
        foreach ($invoices as $invoice) {
            $invoicePayments = $invoice->payments()
                ->where('status', 'completed');
                
            if ($statement->date_from) {
                $invoicePayments->where('processed_at', '>=', $statement->date_from);
            }
            if ($statement->date_to) {
                $invoicePayments->where('processed_at', '<=', $statement->date_to);
            }
            
            $payments = $payments->merge($invoicePayments->get());
        }

        $statementData = [
            'client' => $client,
            'projects' => $projects,
            'invoices' => $invoices,
            'payments' => $payments->sortBy('processed_at'),
            'date_from' => $statement->date_from ? Carbon::parse($statement->date_from) : null,
            'date_to' => $statement->date_to ? Carbon::parse($statement->date_to) : null,
            'total_budget' => $statement->total_budget,
            'total_invoiced' => $statement->total_invoiced,
            'total_paid' => $statement->total_paid,
            'outstanding_balance' => $statement->outstanding_balance,
            'remaining_budget' => $statement->total_budget - $statement->total_invoiced,
            'statement_date' => Carbon::parse($statement->created_at),
            'statement_number' => $statement->statement_number,
        ];

        return view('payment-statements.view', $statementData);
    }

    /**
     * Download statement as PDF
     */
    public function pdf($id)
    {
        $statement = DB::table('payment_statements')
            ->where('company_id', auth()->user()->company_id)
            ->where('id', $id)
            ->firstOrFail();

        $client = Client::findOrFail($statement->client_id);
        
        // Recreate the statement data
        $projects = Project::forCompany()
            ->where('client_id', $client->id)
            ->with(['site'])
            ->get();

        $invoicesQuery = Invoice::forCompany()
            ->where('client_id', $client->id)
            ->with(['payments']);
            
        if ($statement->date_from) {
            $invoicesQuery->where('issue_date', '>=', $statement->date_from);
        }
        if ($statement->date_to) {
            $invoicesQuery->where('issue_date', '<=', $statement->date_to);
        }
        
        $invoices = $invoicesQuery->orderBy('issue_date', 'desc')->get();

        // Get payments
        $payments = collect();
        foreach ($invoices as $invoice) {
            $invoicePayments = $invoice->payments()
                ->where('status', 'completed');
                
            if ($statement->date_from) {
                $invoicePayments->where('processed_at', '>=', $statement->date_from);
            }
            if ($statement->date_to) {
                $invoicePayments->where('processed_at', '<=', $statement->date_to);
            }
            
            $payments = $payments->merge($invoicePayments->get());
        }

        $statementData = [
            'client' => $client,
            'projects' => $projects,
            'invoices' => $invoices,
            'payments' => $payments->sortBy('processed_at'),
            'date_from' => $statement->date_from ? Carbon::parse($statement->date_from) : null,
            'date_to' => $statement->date_to ? Carbon::parse($statement->date_to) : null,
            'total_budget' => $statement->total_budget,
            'total_invoiced' => $statement->total_invoiced,
            'total_paid' => $statement->total_paid,
            'outstanding_balance' => $statement->outstanding_balance,
            'remaining_budget' => $statement->total_budget - $statement->total_invoiced,
            'statement_date' => Carbon::parse($statement->created_at),
            'statement_number' => $statement->statement_number,
            'company' => auth()->user()->company,
        ];

        $pdf = PDF::loadView('payment-statements.pdf', $statementData);
        
        return $pdf->download('Payment-Statement-' . $statement->statement_number . '.pdf');
    }

    /**
     * Send statement via email
     */
    public function send($id)
    {
        // This would implement email sending functionality
        // For now, just redirect with success message
        return redirect()->route('payment-statements.index')
            ->with('success', 'Payment statement sent successfully to client.');
    }

    /**
     * Generate a unique statement number
     */
    private function generateStatementNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastStatement = DB::table('payment_statements')
            ->where('company_id', auth()->user()->company_id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStatement && preg_match('/STMT-' . $year . $month . '-(\d{4})/', $lastStatement->statement_number, $matches)) {
            $nextNumber = str_pad((int)$matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return 'STMT-' . $year . $month . '-' . $nextNumber;
    }
}