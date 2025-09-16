<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\DocumentAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.access');
    }

    /**
     * Show operative profile with tabs
     */
    public function showOperative(Employee $employee, Request $request)
    {
        if (!auth()->user()->canManageProjects()) {
            abort(403, 'Access denied.');
        }

        // Ensure employee belongs to the company
        if ($employee->company_id !== auth()->user()->company_id) {
            abort(404, 'Employee not found.');
        }

        $activeTab = $request->get('tab', 'general');
        
        // Load relationships
        $employee->load([
            'siteAllocations.site',
            'cisPayments' => function($query) {
                $query->latest()->limit(10);
            },
            'documentAttachments' => function($query) {
                $query->latest();
            }
        ]);

        // Get financial summary
        $financialSummary = $this->getFinancialSummary($employee);
        
        // Get operational summary
        $operationalSummary = $this->getOperationalSummary($employee);
        
        // Get document summary
        $documentSummary = $this->getDocumentSummary($employee);

        return view('profiles.operative', compact(
            'employee',
            'activeTab',
            'financialSummary',
            'operationalSummary',
            'documentSummary'
        ));
    }

    /**
     * Show employee profile with tabs
     */
    public function showEmployee(User $user, Request $request)
    {
        if (!auth()->user()->canManageCompanyUsers()) {
            abort(403, 'Access denied.');
        }

        // Ensure user belongs to the company
        if ($user->company_id !== auth()->user()->company_id) {
            abort(404, 'Employee not found.');
        }

        $activeTab = $request->get('tab', 'general');
        
        // Load relationships
        $user->load([
            'managedSites',
            'managedProjects',
            'cisPayments' => function($query) {
                $query->latest()->limit(10);
            },
            'documentAttachments' => function($query) {
                $query->latest();
            }
        ]);

        // Get financial summary
        $financialSummary = $this->getFinancialSummaryForUser($user);
        
        // Get operational summary
        $operationalSummary = $this->getOperationalSummaryForUser($user);
        
        // Get document summary
        $documentSummary = $this->getDocumentSummary($user);

        return view('profiles.employee', compact(
            'user',
            'activeTab',
            'financialSummary',
            'operationalSummary',
            'documentSummary'
        ));
    }

    /**
     * Upload document attachment
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'attachable_type' => 'required|in:Employee,User',
            'attachable_id' => 'required|integer',
            'document_type' => 'required|string',
            'document_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'document_number' => 'nullable|string|max:100',
            'issuing_authority' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . Str::slug($request->document_name) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents/' . $request->attachable_type . '/' . $request->attachable_id, $filename, 'private');

        $document = DocumentAttachment::create([
            'company_id' => auth()->user()->company_id,
            'attachable_type' => 'App\\Models\\' . $request->attachable_type,
            'attachable_id' => $request->attachable_id,
            'document_type' => $request->document_type,
            'document_name' => $request->document_name,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'document_number' => $request->document_number,
            'issuing_authority' => $request->issuing_authority,
            'notes' => $request->notes,
            'uploaded_by' => auth()->id(),
        ]);

        // Update status based on expiry
        $document->updateStatus();

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'document' => $document->load('uploadedBy')
        ]);
    }

    /**
     * Download document
     */
    public function downloadDocument(DocumentAttachment $document)
    {
        // Check permissions
        if ($document->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->download($document->file_path, $document->original_filename);
    }

    /**
     * Delete document
     */
    public function deleteDocument(DocumentAttachment $document)
    {
        // Check permissions
        if ($document->company_id !== auth()->user()->company_id) {
            abort(404);
        }

        // Delete file from storage
        if (Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    private function getFinancialSummary($model)
    {
        $cisPayments = $model->cisPayments;
        
        return [
            'total_gross' => $cisPayments->sum('gross_amount'),
            'total_deductions' => $cisPayments->sum('cis_deduction'),
            'total_net' => $cisPayments->sum('net_payment'),
            'payment_count' => $cisPayments->count(),
            'average_payment' => $cisPayments->avg('gross_amount') ?? 0,
            'last_payment_date' => $cisPayments->first()?->payment_date,
        ];
    }

    private function getFinancialSummaryForUser($user)
    {
        return $this->getFinancialSummary($user);
    }

    private function getOperationalSummary($employee)
    {
        return [
            'site_allocations' => $employee->siteAllocations->count(),
            'active_sites' => $employee->siteAllocations->where('status', 'active')->count(),
            'current_allocation' => $employee->siteAllocations->where('status', 'active')->first(),
            'total_projects' => $employee->siteAllocations->pluck('site.projects')->flatten()->unique('id')->count(),
        ];
    }

    private function getOperationalSummaryForUser($user)
    {
        return [
            'site_allocations' => $user->managedSites->count(),
            'active_sites' => $user->managedSites->where('pivot.is_active', true)->count(),
            'current_allocation' => $user->managedSites->where('pivot.is_active', true)->first(),
            'total_projects' => $user->managedProjects->count(),
            'managed_sites' => $user->managedSites->count(),
            'managed_projects' => $user->managedProjects->count(),
            'active_projects' => $user->managedProjects->where('status', 'in_progress')->count(),
            'role' => $user->role,
        ];
    }

    private function getDocumentSummary($model)
    {
        $documents = $model->documentAttachments ?? collect();
        
        return [
            'total_documents' => $documents->count(),
            'active_documents' => $documents->where('status', DocumentAttachment::STATUS_ACTIVE)->count(),
            'expiring_documents' => $documents->where('status', DocumentAttachment::STATUS_EXPIRING_SOON)->count(),
            'expired_documents' => $documents->where('status', DocumentAttachment::STATUS_EXPIRED)->count(),
            'recent_uploads' => $documents->take(3),
        ];
    }
}