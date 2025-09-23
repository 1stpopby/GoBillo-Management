<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $query = Client::forCompany()->withCount(['projects as total_projects_count', 'projects as active_projects_count' => function ($q) {
            $q->whereIn('status', ['planning', 'in_progress']);
        }])->with('sites');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('legal_name', 'like', "%{$search}%")
                  ->orWhere('contact_person_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_person_email', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%")
                  ->orWhere('business_type', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $clients = $query->orderBy('company_name')->paginate(15);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request)
    {
        // Determine if this is a private client
        $isPrivateClient = $request->boolean('is_private_client');
        
        // Build validation rules based on client type
        $rules = [
            'is_private_client' => 'boolean',
            'contact_person_name' => $isPrivateClient ? 'required|string|max:255' : 'nullable|string|max:255',
            'contact_person_title' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ];

        // Add company-specific rules only for business clients
        if (!$isPrivateClient) {
            $rules = array_merge($rules, [
                'company_name' => 'required|string|max:255',
                'legal_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'website' => 'nullable|url|max:255',
                'tax_id' => 'nullable|string|max:50',
                'business_type' => 'nullable|string|max:100',
                'business_description' => 'nullable|string',
                'industry' => 'nullable|string|max:100',
            ]);
        }
        
        $request->validate($rules);

        // Build client data based on client type
        $clientData = [
            'company_id' => auth()->user()->company_id,
            'is_private_client' => $isPrivateClient,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_title' => $request->contact_person_title,
            'contact_person_email' => $request->contact_person_email,
            'contact_person_phone' => $request->contact_person_phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active')
        ];

        // Add company-specific data only for business clients
        if (!$isPrivateClient) {
            $clientData = array_merge($clientData, [
                'company_name' => $request->company_name,
                'legal_name' => $request->legal_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'tax_id' => $request->tax_id,
                'business_type' => $request->business_type,
                'business_description' => $request->business_description,
                'industry' => $request->industry,
            ]);
        } else {
            // For private clients, use contact person name as company name for display purposes
            $clientData['company_name'] = $request->contact_person_name;
        }

        $client = Client::create($clientData);

        $clientType = $isPrivateClient ? 'Private client' : 'Client company';
        return redirect()->route('clients.show', $client)
                        ->with('success', $clientType . ' created successfully.');
    }

    /**
     * Display the specified client.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        
        $client = Client::with([
                            'projects' => function ($query) {
                                $query->latest();
                            },
                            'sites' => function ($query) {
                                $query->latest();
                            },
                            'invoices' => function ($query) {
                                $query->latest()->limit(10);
                            }
                        ])
                       ->forCompany($user->company_id)
                       ->findOrFail($id);

        // Get invoice statistics
        $invoiceStats = [
            'total' => $client->invoices()->count(),
            'pending' => $client->invoices()->whereIn('status', ['draft', 'sent'])->count(),
            'paid' => $client->invoices()->where('status', 'paid')->count(),
            'overdue' => $client->invoices()->where('status', 'sent')
                                           ->where('due_date', '<', now())
                                           ->count(),
            'total_amount' => $client->invoices()->sum('total_amount'),
            'paid_amount' => $client->invoices()->where('status', 'paid')->sum('total_amount'),
            'pending_amount' => $client->invoices()->whereIn('status', ['draft', 'sent'])->sum('total_amount'),
        ];

        return view('clients.show', compact('client', 'invoiceStats'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        
        $client = Client::forCompany($user->company_id)->findOrFail($id);
        
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        
        $client = Client::forCompany($user->company_id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $client->update($validated);

        return redirect()->route('clients.show', $client)
                        ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified client.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        
        $client = Client::forCompany($user->company_id)->findOrFail($id);

        // Check if client has active projects
        if ($client->projects()->whereIn('status', ['planning', 'in_progress'])->exists()) {
            return back()->with('error', 'Cannot delete client with active projects.');
        }

        $clientName = $client->name;
        $client->delete();

        return redirect()->route('clients.index')
                        ->with('success', "Client '{$clientName}' deleted successfully.");
    }
}
