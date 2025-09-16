<?php

namespace App\Http\Controllers;

use App\Models\OperativeInvoice;
use App\Models\OperativeInvoiceItem;
use App\Models\User;
use App\Models\Site;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OperativeInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = OperativeInvoice::with(['site', 'project', 'manager'])
            ->forCompany($user->company_id);

        // If user is operative, only show their invoices
        if ($user->isOperative()) {
            $query->forOperative($user->id);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->byStatus($request->status);
        }

        $invoices = $query->latest()->paginate(15);

        return view('operative-invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Get operative's employee record to fetch day rate and CIS settings
        $employee = Employee::where('user_id', $user->id)
            ->where('company_id', $user->company_id)
            ->first();
        
        if (!$employee || !$employee->day_rate) {
            return redirect()->back()->with('error', 'Your day rate must be set by the company admin before creating invoices.');
        }

        // Get only users with site manager role who are assigned to sites
        $siteManagerIds = \DB::table('site_managers')
            ->join('sites', 'site_managers.site_id', '=', 'sites.id')
            ->where('sites.company_id', $user->company_id)
            ->where('site_managers.is_active', true)
            ->pluck('site_managers.manager_id');
        
        $managers = User::where('company_id', $user->company_id)
            ->where('role', User::ROLE_SITE_MANAGER)
            ->whereIn('id', $siteManagerIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get sites and projects for dropdowns (will be populated via AJAX)
        $sites = collect();
        $projects = collect();

        return view('operative-invoices.create', compact('employee', 'managers', 'sites', 'projects'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'manager_id' => 'required|exists:users,id',
            'site_id' => 'required|exists:sites,id',
            'project_id' => 'required|exists:projects,id',
            'week_period_start' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:7|max:7',
            'items.*.worked' => 'nullable|boolean',
            'items.*.hours_worked' => 'nullable|numeric|min:0|max:24',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.day_of_week' => 'required|string',
        ]);

        // Get operative's employee record
        $employee = Employee::where('user_id', $user->id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$employee || !$employee->day_rate) {
            return back()->with('error', 'Your day rate must be set by the company admin.');
        }

        // Calculate week ending date
        $weekStarting = Carbon::parse($validated['week_period_start']);
        $weekEnding = $weekStarting->copy()->addDays(6);

        // Create the invoice with submitted status
        $invoice = OperativeInvoice::create([
            'company_id' => $user->company_id,
            'operative_id' => $user->id,
            'manager_id' => $validated['manager_id'],
            'site_id' => $validated['site_id'],
            'project_id' => $validated['project_id'], // Now required for proper cost allocation
            'week_starting' => $weekStarting,
            'week_ending' => $weekEnding,
            'day_rate' => $employee->day_rate,
            'cis_applicable' => $employee->cis_applicable,
            'cis_rate' => $employee->cis_applicable ? $employee->cis_rate : null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'submitted', // Automatically submit for manager approval
            'submitted_at' => now(), // Record submission time
        ]);

        // Create invoice items for each day of the week
        foreach ($validated['items'] as $index => $itemData) {
            $workDate = $weekStarting->copy()->addDays($index);
            
            OperativeInvoiceItem::create([
                'operative_invoice_id' => $invoice->id,
                'day_of_week' => strtolower($itemData['day_of_week']),
                'work_date' => $workDate,
                'worked' => isset($itemData['worked']) && $itemData['worked'],
                'hours_worked' => (isset($itemData['worked']) && $itemData['worked']) ? ($itemData['hours_worked'] ?? 8.00) : 0,
                'description' => (isset($itemData['worked']) && $itemData['worked']) ? ($itemData['description'] ?? null) : null,
                'day_rate' => $employee->day_rate,
            ]);
        }

        // Calculate totals
        $invoice->calculateTotals();

        return redirect()->route('operative-invoices.show', $invoice)
            ->with('success', 'Invoice created and submitted for manager approval successfully!');
    }

    public function show(OperativeInvoice $operativeInvoice)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->isOperative() && $operativeInvoice->operative_id !== $user->id) {
            abort(403, 'You can only view your own invoices.');
        }
        
        if (!$user->isOperative() && $operativeInvoice->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $operativeInvoice->load(['items', 'site', 'project', 'manager', 'operative']);

        return view('operative-invoices.show', compact('operativeInvoice'));
    }

    public function edit(OperativeInvoice $operativeInvoice)
    {
        $user = auth()->user();
        
        // Operatives cannot edit invoices once created - they are automatically submitted for approval
        if ($user->isOperative()) {
            return redirect()->route('operative-invoices.show', $operativeInvoice)
                ->with('error', 'Invoices cannot be edited once created. They are automatically submitted for manager approval.');
        }
        
        // Only managers and admins can edit invoices for approval workflow
        if ($operativeInvoice->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        // Only allow editing of draft invoices
        if ($operativeInvoice->status !== 'draft') {
            return redirect()->route('operative-invoices.show', $operativeInvoice)
                ->with('error', 'Only draft invoices can be edited.');
        }

        // Get operative's employee record
        $employee = Employee::where('user_id', $user->id)
            ->where('company_id', $user->company_id)
            ->first();

        // Get only users with site manager role who are assigned to sites
        $siteManagerIds = \DB::table('site_managers')
            ->join('sites', 'site_managers.site_id', '=', 'sites.id')
            ->where('sites.company_id', $user->company_id)
            ->where('site_managers.is_active', true)
            ->pluck('site_managers.manager_id');
        
        $managers = User::where('company_id', $user->company_id)
            ->where('role', User::ROLE_SITE_MANAGER)
            ->whereIn('id', $siteManagerIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get sites and projects for the current selection
        $sites = Site::where('company_id', $user->company_id)
            ->where('status', 'active')
            ->get();

        $projects = Project::where('company_id', $user->company_id)
            ->where('site_id', $operativeInvoice->site_id)
            ->where('status', 'active')
            ->get();

        $operativeInvoice->load(['items']);

        return view('operative-invoices.edit', compact('operativeInvoice', 'employee', 'managers', 'sites', 'projects'));
    }

    public function update(Request $request, OperativeInvoice $operativeInvoice)
    {
        $user = auth()->user();
        
        // Operatives cannot update invoices once created - they are automatically submitted for approval
        if ($user->isOperative()) {
            return redirect()->route('operative-invoices.show', $operativeInvoice)
                ->with('error', 'Invoices cannot be updated once created. They are automatically submitted for manager approval.');
        }
        
        // Only managers and admins can update invoices for approval workflow
        if ($operativeInvoice->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        // Only allow updating of draft invoices
        if ($operativeInvoice->status !== 'draft') {
            return redirect()->route('operative-invoices.show', $operativeInvoice)
                ->with('error', 'Only draft invoices can be updated.');
        }

        $validated = $request->validate([
            'manager_id' => 'required|exists:users,id',
            'site_id' => 'required|exists:sites,id',
            'week_period_start' => 'required|date',
            'gross_amount' => 'required|numeric|min:0',
            'cis_applicable' => 'boolean',
            'cis_rate' => 'nullable|numeric|min:0|max:100',
            'cis_deduction' => 'nullable|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.day_of_week' => 'required|string',
            'items.*.worked' => 'boolean',
            'items.*.hours_worked' => 'nullable|numeric|min:0|max:24',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.amount' => 'nullable|numeric|min:0',
        ]);

        // Calculate week end date
        $weekStart = Carbon::parse($validated['week_period_start']);
        $weekEnd = $weekStart->copy()->endOfWeek();

        // Update invoice
        $operativeInvoice->update([
            'manager_id' => $validated['manager_id'],
            'site_id' => $validated['site_id'],
            'project_id' => null, // No longer required
            'week_period_start' => $weekStart,
            'week_period_end' => $weekEnd,
            'gross_amount' => $validated['gross_amount'],
            'cis_applicable' => $request->has('cis_applicable'),
            'cis_rate' => $request->has('cis_applicable') ? $validated['cis_rate'] : null,
            'cis_deduction' => $request->has('cis_applicable') ? $validated['cis_deduction'] : 0,
            'net_amount' => $validated['net_amount'],
        ]);

        // Update invoice items
        foreach ($validated['items'] as $index => $itemData) {
            $item = $operativeInvoice->items->get($index);
            if ($item) {
                $item->update([
                    'worked' => isset($itemData['worked']),
                    'hours_worked' => isset($itemData['worked']) ? ($itemData['hours_worked'] ?? 0) : 0,
                    'description' => isset($itemData['worked']) ? ($itemData['description'] ?? '') : '',
                    'amount' => isset($itemData['worked']) ? ($itemData['amount'] ?? 0) : 0,
                ]);
            }
        }

        return redirect()->route('operative-invoices.show', $operativeInvoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function submit(Request $request, OperativeInvoice $operativeInvoice)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->isOperative() && $operativeInvoice->operative_id !== $user->id) {
            abort(403, 'You can only submit your own invoices.');
        }
        
        if (!$user->isOperative() && $operativeInvoice->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        // Only allow submitting of draft invoices
        if ($operativeInvoice->status !== 'draft') {
            return redirect()->route('operative-invoices.show', $operativeInvoice)
                ->with('error', 'Only draft invoices can be submitted.');
        }

        $operativeInvoice->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('operative-invoices.show', $operativeInvoice)
            ->with('success', 'Invoice submitted successfully.');
    }

    // AJAX endpoints
    public function getSitesForManager(Request $request)
    {
        $managerId = $request->get('manager_id');
        $user = auth()->user();
        
        if (!$managerId) {
            return response()->json([]);
        }
        
        // Get sites where the manager is assigned using the new many-to-many relationship
        $sites = Site::where('company_id', $user->company_id)
            ->where('status', 'active')
            ->whereHas('managers', function ($query) use ($managerId) {
                $query->where('users.id', $managerId)
                      ->where('site_managers.is_active', true);
            })
            ->get(['id', 'name']);

        return response()->json($sites);
    }

}