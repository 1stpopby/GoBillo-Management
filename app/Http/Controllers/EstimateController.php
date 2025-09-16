<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\EstimateTemplate;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EstimateController extends Controller
{
    /**
     * Display a listing of estimates
     */
    public function index(Request $request)
    {
        $query = Estimate::forCompany()->with(['client', 'project']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('estimate_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        $estimates = $query->latest()->paginate(15);

        // Check for expired estimates and update status
        foreach ($estimates as $estimate) {
            $estimate->checkExpired();
        }

        // Get filter options
        $clients = Client::forCompany()->orderBy('name')->get();
        $projects = Project::forCompany()->orderBy('name')->get();

        return view('estimates.index', compact('estimates', 'clients', 'projects'));
    }

    /**
     * Show the form for creating a new estimate
     */
    public function create(Request $request)
    {
        $clients = Client::forCompany()->orderBy('name')->get();
        $projects = Project::forCompany()->orderBy('name')->get();
        $templates = EstimateTemplate::forCompany()->active()->orderBy('name')->get();

        $selectedTemplate = null;
        if ($request->filled('template_id')) {
            $selectedTemplate = EstimateTemplate::forCompany()->find($request->template_id);
        }

        return view('estimates.create', compact('clients', 'projects', 'templates', 'selectedTemplate'));
    }

    /**
     * Store a newly created estimate
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date|after:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.category' => 'nullable|string|max:100',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.markup_percentage' => 'nullable|numeric|min:0|max:1000',
        ]);

        DB::transaction(function () use ($request) {
            $estimate = Estimate::create([
                'company_id' => auth()->user()->company_id,
                'client_id' => $request->client_id,
                'project_id' => $request->project_id,
                'issue_date' => $request->issue_date,
                'valid_until' => $request->valid_until,
                'tax_rate' => $request->tax_rate ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'currency' => $request->currency,
                'description' => $request->description,
                'terms' => $request->terms,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $index => $itemData) {
                EstimateItem::create([
                    'estimate_id' => $estimate->id,
                    'category' => $itemData['category'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'markup_percentage' => $itemData['markup_percentage'] ?? 0,
                    'sort_order' => $index,
                ]);
            }

            $estimate->calculateTotals();
        });

        return redirect()->route('estimates.index')
                        ->with('success', 'Estimate created successfully.');
    }

    /**
     * Display the specified estimate
     */
    public function show(Estimate $estimate)
    {
        $this->authorize('view', $estimate);
        
        $estimate->load(['client', 'project', 'items', 'convertedToProject']);
        
        return view('estimates.show', compact('estimate'));
    }

    /**
     * Show the form for editing the specified estimate
     */
    public function edit(Estimate $estimate)
    {
        $this->authorize('update', $estimate);
        
        if (in_array($estimate->status, [Estimate::STATUS_APPROVED, Estimate::STATUS_CONVERTED])) {
            return back()->with('error', 'Cannot edit approved or converted estimates.');
        }

        $estimate->load('items');
        $clients = Client::forCompany()->orderBy('name')->get();
        $projects = Project::forCompany()->orderBy('name')->get();

        return view('estimates.edit', compact('estimate', 'clients', 'projects'));
    }

    /**
     * Update the specified estimate
     */
    public function update(Request $request, Estimate $estimate)
    {
        $this->authorize('update', $estimate);
        
        if (in_array($estimate->status, [Estimate::STATUS_APPROVED, Estimate::STATUS_CONVERTED])) {
            return back()->with('error', 'Cannot edit approved or converted estimates.');
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date|after:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.category' => 'nullable|string|max:100',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.markup_percentage' => 'nullable|numeric|min:0|max:1000',
        ]);

        DB::transaction(function () use ($request, $estimate) {
            $estimate->update([
                'client_id' => $request->client_id,
                'project_id' => $request->project_id,
                'issue_date' => $request->issue_date,
                'valid_until' => $request->valid_until,
                'tax_rate' => $request->tax_rate ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'currency' => $request->currency,
                'description' => $request->description,
                'terms' => $request->terms,
                'notes' => $request->notes,
            ]);

            // Delete existing items and recreate
            $estimate->items()->delete();

            foreach ($request->items as $index => $itemData) {
                EstimateItem::create([
                    'estimate_id' => $estimate->id,
                    'category' => $itemData['category'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'markup_percentage' => $itemData['markup_percentage'] ?? 0,
                    'sort_order' => $index,
                ]);
            }

            $estimate->calculateTotals();
        });

        return redirect()->route('estimates.show', $estimate)
                        ->with('success', 'Estimate updated successfully.');
    }

    /**
     * Remove the specified estimate
     */
    public function destroy(Estimate $estimate)
    {
        $this->authorize('delete', $estimate);
        
        if (in_array($estimate->status, [Estimate::STATUS_APPROVED, Estimate::STATUS_CONVERTED])) {
            return back()->with('error', 'Cannot delete approved or converted estimates.');
        }

        $estimateNumber = $estimate->estimate_number;
        $estimate->delete();

        return redirect()->route('estimates.index')
                        ->with('success', "Estimate {$estimateNumber} deleted successfully.");
    }

    /**
     * Send estimate to client
     */
    public function send(Estimate $estimate)
    {
        $this->authorize('update', $estimate);
        
        if (in_array($estimate->status, [Estimate::STATUS_APPROVED, Estimate::STATUS_CONVERTED])) {
            return back()->with('error', 'Estimate is already processed.');
        }

        $estimate->markAsSent();

        return back()->with('success', 'Estimate sent successfully.');
    }

    /**
     * Mark estimate as approved
     */
    public function approve(Estimate $estimate)
    {
        $this->authorize('update', $estimate);
        
        $estimate->markAsApproved();

        return back()->with('success', 'Estimate approved successfully.');
    }

    /**
     * Mark estimate as rejected
     */
    public function reject(Request $request, Estimate $estimate)
    {
        $this->authorize('update', $estimate);
        
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $estimate->markAsRejected($request->rejection_reason);

        return back()->with('success', 'Estimate rejected.');
    }

    /**
     * Convert estimate to project
     */
    public function convertToProject(Request $request, Estimate $estimate)
    {
        $this->authorize('update', $estimate);
        
        if ($estimate->status !== Estimate::STATUS_APPROVED) {
            return back()->with('error', 'Only approved estimates can be converted to projects.');
        }

        $request->validate([
            'project_name' => 'required|string|max:255',
            'project_description' => 'nullable|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $project = $estimate->convertToProject([
            'name' => $request->project_name,
            'description' => $request->project_description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('projects.show', $project)
                        ->with('success', 'Estimate converted to project successfully.');
    }

    /**
     * Generate PDF for estimate
     */
    public function pdf(Estimate $estimate)
    {
        $this->authorize('view', $estimate);
        
        $estimate->load(['client', 'project', 'items', 'company']);

        $pdf = Pdf::loadView('estimates.pdf', compact('estimate'));
        
        return $pdf->download("estimate-{$estimate->estimate_number}.pdf");
    }

    /**
     * Duplicate estimate
     */
    public function duplicate(Estimate $estimate)
    {
        $this->authorize('view', $estimate);
        
        $newEstimate = $estimate->replicate();
        $newEstimate->status = Estimate::STATUS_DRAFT;
        $newEstimate->estimate_number = null; // Will be auto-generated
        $newEstimate->sent_at = null;
        $newEstimate->approved_at = null;
        $newEstimate->rejected_at = null;
        $newEstimate->rejection_reason = null;
        $newEstimate->converted_to_project_id = null;
        $newEstimate->issue_date = now()->toDateString();
        $newEstimate->valid_until = now()->addDays(30)->toDateString();
        $newEstimate->save();

        foreach ($estimate->items as $item) {
            $newItem = $item->replicate();
            $newItem->estimate_id = $newEstimate->id;
            $newItem->save();
        }

        $newEstimate->calculateTotals();

        return redirect()->route('estimates.edit', $newEstimate)
                        ->with('success', 'Estimate duplicated successfully.');
    }

    /**
     * Create estimate from template
     */
    public function createFromTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:estimate_templates,id',
            'client_id' => 'required|exists:clients,id',
        ]);

        $template = EstimateTemplate::forCompany()->findOrFail($request->template_id);
        $estimate = $template->createEstimate($request->client_id);

        return redirect()->route('estimates.edit', $estimate)
                        ->with('success', 'Estimate created from template successfully.');
    }
} 