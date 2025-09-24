<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectVariation;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;
use App\Services\VariationPDFService;

class ProjectVariationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $variations = $project->projectVariations()
            ->with(['creator', 'approver'])
            ->latest()
            ->get();

        return response()->json($variations);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $tasks = $project->tasks()->select('id', 'title')->get();
        
        return response()->json([
            'variation_number' => ProjectVariation::generateVariationNumber($project->id),
            'types' => [
                'addition' => 'Addition',
                'omission' => 'Omission',
                'substitution' => 'Substitution',
                'change_order' => 'Change Order'
            ],
            'tasks' => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'reason' => 'required|string',
            'type' => 'required|in:addition,omission,substitution,change_order',
            'cost_impact' => 'required|numeric',
            'time_impact_days' => 'required|integer',
            'requested_date' => 'required|date',
            'required_by_date' => 'nullable|date|after:requested_date',
            'client_reference' => 'nullable|string|max:255',
            'affected_tasks' => 'nullable|array',
            'affected_tasks.*' => 'exists:tasks,id'
        ]);

        $validated['project_id'] = $project->id;
        $validated['company_id'] = auth()->user()->company_id;
        $validated['created_by'] = auth()->id();
        $validated['variation_number'] = ProjectVariation::generateVariationNumber($project->id);

        $variation = ProjectVariation::create($validated);
        $variation->load(['creator', 'approver']);

        // Send notification to company admins when a manager submits a variation
        if (in_array(auth()->user()->role, ['site_manager', 'project_manager'])) {
            $this->notifyAdminsOfNewVariation($variation, $project);
        }

        return response()->json([
            'success' => true,
            'message' => 'Variation created successfully',
            'variation' => $variation
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, ProjectVariation $variation)
    {
        $variation->load(['creator', 'approver']);
        
        // Load affected tasks if any
        if ($variation->affected_tasks) {
            $affectedTasks = Task::whereIn('id', $variation->affected_tasks)
                ->select('id', 'title', 'status')
                ->get();
            $variation->affected_tasks_details = $affectedTasks;
        }

        return view('project-variations.show', compact('project', 'variation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, ProjectVariation $variation)
    {
        // Only allow editing if not approved or implemented
        if (in_array($variation->status, ['approved', 'implemented'])) {
            return redirect()->route('project.variations.show', ['project' => $project, 'variation' => $variation])
                ->with('error', 'Cannot edit approved or implemented variations');
        }
        
        $variation->load(['creator', 'approver']);
        $tasks = $project->tasks()->select('id', 'title')->get();
        
        $types = [
            'addition' => 'Addition',
            'omission' => 'Omission',
            'substitution' => 'Substitution',
            'change_order' => 'Change Order'
        ];
        
        return view('project-variations.edit', compact('project', 'variation', 'tasks', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, ProjectVariation $variation)
    {
        // Only allow editing if not approved or implemented
        if (in_array($variation->status, ['approved', 'implemented'])) {
            return redirect()->route('project.variations.show', ['project' => $project, 'variation' => $variation])
                ->with('error', 'Cannot edit approved or implemented variations');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'reason' => 'required|string',
            'type' => 'required|in:addition,omission,substitution,change_order',
            'cost_impact' => 'required|numeric',
            'time_impact_days' => 'required|integer',
            'requested_date' => 'required|date',
            'required_by_date' => 'nullable|date|after:requested_date',
            'client_reference' => 'nullable|string|max:255',
            'affected_tasks' => 'nullable|array',
            'affected_tasks.*' => 'exists:tasks,id'
        ]);

        $variation->update($validated);

        return redirect()->route('project.variations.show', ['project' => $project, 'variation' => $variation])
            ->with('success', 'Variation updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectVariation $variation)
    {
        // Only allow deletion if not approved or implemented
        if (in_array($variation->status, ['approved', 'implemented'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete approved or implemented variations'
            ], 422);
        }

        $variation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variation deleted successfully'
        ]);
    }

    /**
     * Approve a variation
     */
    public function approve(Project $project, ProjectVariation $variation)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Unauthorized to approve variations');
        }

        $variation->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        $variation->load(['creator', 'approver']);

        return response()->json([
            'success' => true,
            'message' => 'Variation approved successfully',
            'variation' => $variation
        ]);
    }

    /**
     * Reject a variation
     */
    public function reject(Request $request, Project $project, ProjectVariation $variation)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Unauthorized to reject variations');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string'
        ]);

        $variation->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes']
        ]);

        $variation->load(['creator', 'approver']);

        return response()->json([
            'success' => true,
            'message' => 'Variation rejected',
            'variation' => $variation
        ]);
    }

    /**
     * Mark variation as implemented
     */
    public function implement(Project $project, ProjectVariation $variation)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Unauthorized to implement variations');
        }

        if ($variation->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved variations can be implemented'
            ], 422);
        }

        $variation->update([
            'status' => 'implemented'
        ]);

        // Update project budget if there's a cost impact
        if ($variation->cost_impact != 0) {
            $project->increment('budget', $variation->cost_impact);
        }

        // Update project end date if there's a time impact
        if ($variation->time_impact_days != 0 && $project->end_date) {
            $project->update([
                'end_date' => $project->end_date->addDays($variation->time_impact_days)
            ]);
        }

        $variation->load(['creator', 'approver']);

        return response()->json([
            'success' => true,
            'message' => 'Variation implemented successfully',
            'variation' => $variation
        ]);
    }

    /**
     * Send variation email to client
     */
    public function sendEmail(Request $request, Project $project, ProjectVariation $variation)
    {
        try {
            \Log::info('Email send attempt started', [
                'project_id' => $project->id,
                'variation_id' => $variation->id,
                'user_id' => auth()->id()
            ]);

            if (!auth()->user()->canManageProjects()) {
                \Log::warning('User cannot manage projects');
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to send variation emails'
                ], 403);
            }

            \Log::info('User can manage projects, proceeding with validation');
        } catch (\Exception $e) {
            \Log::error('Error in sendEmail method start: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error in initial setup: ' . $e->getMessage()
            ], 500);
        }

        try {
            \Log::info('Starting validation', ['request_data' => $request->except(['_token'])]);
            
            $validated = $request->validate([
                'client_email' => 'required|email',
                'client_name' => 'required|string|max:255',
                'email_subject' => 'required|string|max:255',
                'email_message' => 'required|string',
                'include_pdf' => 'nullable',
            ]);
            
            // Handle checkbox value
            $validated['include_pdf'] = $request->has('include_pdf');
            
            \Log::info('Validation passed', ['validated_data' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', Arr::flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Unexpected error during validation', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error during validation: ' . $e->getMessage()
            ], 500);
        }

        try {
            // Check if email settings exist first
            $emailSetting = \App\Models\EmailSetting::forCompany(auth()->user()->company_id)->first();
            if (!$emailSetting || !$emailSetting->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active email configuration found. Please configure your email settings first.'
                ], 422);
            }

            // Configure Laravel's mail settings
            $emailSetting->configureMailer();
            
            // Prepare email data
            $client = (object) [
                'email' => $validated['client_email'],
                'name' => $validated['client_name'],
                'company_name' => $project->client->company_name ?? $validated['client_name'],
                'contact_name' => $validated['client_name'],
            ];
            
            $company = auth()->user()->company;

            // Send email directly using Laravel's Mail facade
            \Mail::send('emails.templates.project_variation_created', [
                'variation' => $variation,
                'project' => $project,
                'client' => $client,
                'company' => $company,
            ], function ($message) use ($validated, $variation, $project) {
                $message->to($validated['client_email'], $validated['client_name'])
                        ->subject($validated['email_subject']);
                
                // Attach PDF if requested
                if ($validated['include_pdf']) {
                    try {
                        $pdfContent = $this->generateVariationPDF($variation, $project);
                        $filename = 'Variation_' . $variation->variation_number . '.pdf';
                        $message->attachData($pdfContent, $filename, [
                            'mime' => 'application/pdf',
                        ]);
                        \Log::info('PDF attached to email', ['filename' => $filename]);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to attach PDF to email: ' . $e->getMessage());
                        // Continue sending email without PDF
                    }
                }
            });

            // Update email usage statistics
            $emailSetting->increment('emails_sent_today');
            $emailSetting->increment('emails_sent_month');

            return response()->json([
                'success' => true,
                'message' => 'Variation email sent successfully to ' . $validated['client_email']
            ]);

        } catch (\Exception $e) {
            \Log::error('Variation email send error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'variation_id' => $variation->id,
                'project_id' => $project->id,
                'client_email' => $validated['client_email']
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview variation email
     */
    public function previewEmail(Project $project, ProjectVariation $variation)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Unauthorized to preview variation emails');
        }

        try {
            $client = (object) [
                'email' => $project->client->contact_person_email ?: $project->client->email,
                'name' => $project->client->contact_person_name ?: $project->client->company_name,
                'company_name' => $project->client->company_name,
                'contact_name' => $project->client->contact_person_name ?: 'Client',
            ];

            $company = auth()->user()->company;

            $html = view('emails.templates.project_variation_created', [
                'variation' => $variation,
                'project' => $project,
                'client' => $client,
                'company' => $company,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate email preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF for variation (for email attachment)
     */
    private function generateVariationPDF($variation, $project)
    {
        $client = (object) [
            'email' => $project->client->contact_person_email ?: $project->client->email,
            'name' => $project->client->contact_person_name ?: $project->client->company_name,
            'company_name' => $project->client->company_name,
            'contact_name' => $project->client->contact_person_name ?: 'Client',
        ];
        
        $company = auth()->user()->company;

        // Use the TCPDF service to generate a professional PDF
        $pdfService = new VariationPDFService();
        return $pdfService->generateVariationPDF($variation, $project, $client, $company);
    }

    /**
     * Generate PDF for variation (public download)
     */
    public function generatePDF(Project $project, ProjectVariation $variation)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Unauthorized to generate variation PDFs');
        }

        try {
            $client = (object) [
                'email' => $project->client->contact_person_email ?: $project->client->email,
                'name' => $project->client->contact_person_name ?: $project->client->company_name,
                'company_name' => $project->client->company_name,
                'contact_name' => $project->client->contact_person_name ?: 'Client',
            ];
            
            $company = auth()->user()->company;

            // Use the TCPDF service to generate a professional PDF
            $pdfService = new VariationPDFService();
            $pdfContent = $pdfService->generateVariationPDF($variation, $project, $client, $company);
            
            $filename = 'Variation_' . $variation->variation_number . '.pdf';
            
            // Return PDF for download
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return redirect()->route('project.variations.show', [
                'project' => $project,
                'variation' => $variation
            ])->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to company admins when a manager submits a variation
     */
    /**
     * Quick update cost for a variation (Admin only)
     */
    public function quickCostUpdate(Request $request, Project $project, ProjectVariation $variation)
    {
        // Only allow admins to update costs
        if (auth()->user()->role !== 'company_admin') {
            abort(403, 'Unauthorized to update variation costs');
        }

        // Only allow cost updates for pending variations
        if (in_array($variation->status, ['approved', 'implemented', 'rejected'])) {
            return redirect()->route('project.variations.show', ['project' => $project, 'variation' => $variation])
                ->with('error', 'Cannot update cost for variations that are already approved, rejected, or implemented');
        }

        $validated = $request->validate([
            'cost_impact' => 'required|numeric'
        ]);

        $variation->update([
            'cost_impact' => $validated['cost_impact']
        ]);

        return redirect()->route('project.variations.edit', ['project' => $project, 'variation' => $variation])
            ->with('success', 'Cost updated successfully. You can now continue editing other details.');
    }

    private function notifyAdminsOfNewVariation(ProjectVariation $variation, Project $project)
    {
        try {
            // Get all company admins
            $admins = \App\Models\User::where('company_id', auth()->user()->company_id)
                ->where('role', 'admin')
                ->get();

            if ($admins->isEmpty()) {
                return;
            }

            // Prepare notification data
            $notificationData = [
                'subject' => 'New Variation Submitted for Approval',
                'title' => 'New Variation Requires Your Attention',
                'message' => sprintf(
                    '%s has submitted a new variation for project "%s" that requires cost agreement and approval.',
                    auth()->user()->name,
                    $project->name
                ),
                'variation_title' => $variation->title,
                'variation_number' => $variation->variation_number,
                'project_name' => $project->name,
                'submitted_by' => auth()->user()->name,
                'requested_date' => $variation->requested_date,
                'type' => ucfirst(str_replace('_', ' ', $variation->type)),
                'description' => $variation->description,
                'reason' => $variation->reason,
                'action_url' => route('project.variations.show', [
                    'project' => $project->id,
                    'variation' => $variation->id
                ])
            ];

            // Send email to each admin
            foreach ($admins as $admin) {
                try {
                    Mail::send('emails.variation-notification', $notificationData, function ($message) use ($admin, $notificationData) {
                        $message->to($admin->email, $admin->name)
                                ->subject($notificationData['subject']);
                    });
                } catch (\Exception $e) {
                    // Log the error but don't stop the process
                    \Log::error('Failed to send variation notification to ' . $admin->email . ': ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't stop the main process
            \Log::error('Failed to send variation notifications: ' . $e->getMessage());
        }
    }
}
