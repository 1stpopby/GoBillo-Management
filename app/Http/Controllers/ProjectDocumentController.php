<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $documents = $project->projectDocuments()
            ->with(['uploader'])
            ->latest()
            ->get();

        return response()->json($documents);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        return response()->json([
            'categories' => [
                'plans' => 'Plans & Drawings',
                'photos' => 'Photos',
                'contracts' => 'Contracts',
                'permits' => 'Permits',
                'reports' => 'Reports',
                'specifications' => 'Specifications',
                'invoices' => 'Invoices',
                'certificates' => 'Certificates',
                'other' => 'Other'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:plans,photos,contracts,permits,reports,specifications,invoices,certificates,other',
            'is_public' => 'boolean',
            'tags' => 'nullable|string',
            'file' => 'required|file|max:50240' // 50MB max
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('project-documents/' . $project->id, $filename, 'public');

        // Determine file type
        $mimeType = $file->getMimeType();
        $fileType = $this->determineFileType($mimeType);

        // Process tags
        $tags = $request->tags ? explode(',', $request->tags) : null;
        if ($tags) {
            $tags = array_map('trim', $tags);
        }

        $document = ProjectDocument::create([
            'project_id' => $project->id,
            'company_id' => auth()->user()->company_id,
            'uploaded_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'original_filename' => $originalName,
            'file_path' => $path,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'category' => $validated['category'],
            'is_public' => $validated['is_public'] ?? false,
            'tags' => $tags
        ]);

        $document->load(['uploader']);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'document' => $document
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, ProjectDocument $document)
    {
        $document->load(['uploader', 'childDocuments']);
        return view('project-documents.show', compact('project', 'document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, ProjectDocument $document)
    {
        $document->load(['uploader']);
        return response()->json([
            'document' => $document,
            'categories' => [
                'plans' => 'Plans & Drawings',
                'photos' => 'Photos',
                'contracts' => 'Contracts',
                'permits' => 'Permits',
                'reports' => 'Reports',
                'specifications' => 'Specifications',
                'invoices' => 'Invoices',
                'certificates' => 'Certificates',
                'other' => 'Other'
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, ProjectDocument $document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:plans,photos,contracts,permits,reports,specifications,invoices,certificates,other',
            'is_public' => 'boolean',
            'tags' => 'nullable|string',
            'file' => 'nullable|file|max:50240' // 50MB max
        ]);

        // Process tags
        $tags = $request->tags ? explode(',', $request->tags) : null;
        if ($tags) {
            $tags = array_map('trim', $tags);
        }

        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'is_public' => $validated['is_public'] ?? false,
            'tags' => $tags
        ];

        // Handle file replacement
        if ($request->hasFile('file')) {
            // Delete old file
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('project-documents/' . $project->id, $filename, 'public');

            $mimeType = $file->getMimeType();
            $fileType = $this->determineFileType($mimeType);

            $updateData = array_merge($updateData, [
                'original_filename' => $originalName,
                'file_path' => $path,
                'file_type' => $fileType,
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
                'version' => $document->version + 1
            ]);
        }

        $document->update($updateData);
        $document->load(['uploader']);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'document' => $document
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectDocument $document)
    {
        // Delete file
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    /**
     * Download the specified document.
     */
    public function download(Project $project, ProjectDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($document->file_path, $document->original_filename);
    }

    /**
     * View/preview the document inline without downloading.
     */
    public function view(Project $project, ProjectDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        $mimeType = $document->mime_type ?: mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"',
        ]);
    }

    /**
     * Create a new version of the document.
     */
    public function newVersion(Request $request, Project $project, ProjectDocument $document)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:50240'
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filename = time() . '_v' . ($document->version + 1) . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('project-documents/' . $project->id, $filename, 'public');

        $mimeType = $file->getMimeType();
        $fileType = $this->determineFileType($mimeType);

        $newDocument = ProjectDocument::create([
            'project_id' => $project->id,
            'company_id' => auth()->user()->company_id,
            'uploaded_by' => auth()->id(),
            'title' => $document->title . ' (v' . ($document->version + 1) . ')',
            'description' => $document->description,
            'original_filename' => $originalName,
            'file_path' => $path,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'category' => $document->category,
            'is_public' => $document->is_public,
            'tags' => $document->tags,
            'version' => $document->version + 1,
            'parent_document_id' => $document->id
        ]);

        $newDocument->load(['uploader']);

        return response()->json([
            'success' => true,
            'message' => 'New document version created successfully',
            'document' => $newDocument
        ]);
    }

    /**
     * Determine file type based on MIME type
     */
    private function determineFileType($mimeType)
    {
        if (str_contains($mimeType, 'image/')) {
            return 'image';
        } elseif (str_contains($mimeType, 'pdf')) {
            return 'pdf';
        } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
            return 'document';
        } elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet')) {
            return 'spreadsheet';
        } elseif (str_contains($mimeType, 'video/')) {
            return 'video';
        } else {
            return 'other';
        }
    }
}
