<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents for the authenticated user's company.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Document::with(['project', 'uploadedBy'])
                         ->forCompany($user->company_id);

        // Role-based filtering
        if (!$user->canManageProjects()) {
            $query->where(function ($q) use ($user) {
                // Include documents without projects OR documents from user's projects
                $q->whereNull('project_id')
                  ->orWhereHas('project', function ($subQ) use ($user) {
                      $subQ->whereHas('users', function ($userQ) use ($user) {
                          $userQ->where('user_id', $user->id);
                      });
                  });
            });
        }

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('original_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $documents = $query->latest()->paginate(12);
        
        // Get projects for filter dropdown (only from same company)
        $projects = Project::forCompany($user->company_id)
                          ->where('status', '!=', 'cancelled')
                          ->get();

        return view('documents.index', compact('documents', 'projects'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Get projects from same company
        $projects = Project::forCompany($user->company_id)
                          ->where('status', '!=', 'cancelled')
                          ->get();

        return view('documents.create', compact('projects'));
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:blueprint,contract,permit,photo,report,other',
            'project_id' => 'nullable|exists:projects,id',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        // Ensure project belongs to same company if provided
        if ($validated['project_id']) {
            $project = Project::forCompany($user->company_id)->findOrFail($validated['project_id']);
        }

        // Handle file upload
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . $originalName;
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Create document record
        $document = Document::create([
            'company_id' => $user->company_id,
            'name' => $validated['name'],
            'original_name' => $originalName,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'project_id' => $validated['project_id'] ?? null,
            'task_id' => null,
            'uploaded_by' => $user->id,
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('documents.index')
                        ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified document.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        
        $document = Document::with(['project', 'task', 'uploadedBy'])
                           ->forCompany($user->company_id)
                           ->findOrFail($id);

        // Check if user can view this document
        if (!$user->canManageProjects() && $document->project) {
            if (!$document->project->users->contains($user->id)) {
                abort(403, 'Access denied to this document.');
            }
        }

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        
        $document = Document::forCompany($user->company_id)->findOrFail($id);
        
        // Get projects from same company
        $projects = Project::forCompany($user->company_id)
                          ->where('status', '!=', 'cancelled')
                          ->get();

        return view('documents.edit', compact('document', 'projects'));
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        
        $document = Document::forCompany($user->company_id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:blueprint,contract,permit,photo,report,other',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        // Ensure project belongs to same company if provided
        if ($validated['project_id']) {
            $project = Project::forCompany($user->company_id)->findOrFail($validated['project_id']);
        }

        $document->update($validated);

        return redirect()->route('documents.show', $document)
                        ->with('success', 'Document updated successfully.');
    }

    /**
     * Remove the specified document.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        
        $document = Document::forCompany($user->company_id)->findOrFail($id);

        // Delete the file from storage
        if (file_exists(storage_path('app/public/' . $document->file_path))) {
            unlink(storage_path('app/public/' . $document->file_path));
        }

        $documentName = $document->name;
        $document->delete();

        return redirect()->route('documents.index')
                        ->with('success', "Document '{$documentName}' deleted successfully.");
    }

    /**
     * Download the specified document.
     */
    public function download(string $id)
    {
        $user = auth()->user();
        
        $document = Document::forCompany($user->company_id)->findOrFail($id);

        // Check if user can download this document
        if (!$user->canManageProjects() && $document->project) {
            if (!$document->project->users->contains($user->id)) {
                abort(403, 'Access denied to this document.');
            }
        }

        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $document->original_name);
    }
}
