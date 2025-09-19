<?php

namespace App\Http\Controllers;

use App\Models\OperativeDataForm;
use App\Models\Company;
use App\Models\User;
use App\Models\Employee;
use App\Models\DocumentAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OperativeDataFormController extends Controller
{
    // Admin methods for managing forms
    public function index()
    {
        $user = auth()->user();
        
        $forms = OperativeDataForm::where('company_id', $user->company_id)
            ->with(['approvedBy', 'createdUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.operative-data-forms.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.operative-data-forms.create');
    }

    public function generateShareLink(Request $request)
    {
        $user = auth()->user();
        
        $form = OperativeDataForm::create([
            'company_id' => $user->company_id,
            'share_token' => Str::random(32),
            'status' => OperativeDataForm::STATUS_PENDING,
            // Initialize with empty values - will be filled by operative
            'full_name' => '',
            'date_of_birth' => now()->subYears(25), // Default placeholder
            'nationality' => '',
            'mobile_number' => '',
            'email_address' => '',
            'home_address' => '',
            'postcode' => '',
            'emergency_contact_name' => '',
            'emergency_contact_relationship' => '',
            'emergency_contact_number' => '',
            'national_insurance_number' => '',
            'right_to_work_uk' => false,
            'passport_id_provided' => false,
            'bank_name' => '',
            'account_holder_name' => '',
            'sort_code' => '',
            'account_number' => '',
            'primary_trade' => '',
            'years_experience' => 0,
            'declaration_confirmed' => false,
        ]);

        return redirect()->route('admin.operative-data-forms.index')
            ->with('success', 'Share link generated successfully!')
            ->with('share_url', $form->share_url);
    }

    public function show(OperativeDataForm $form)
    {
        $this->authorize('view', $form);
        
        $form->load(['approvedBy', 'createdUser', 'accountCreatedBy']);
        
        return view('admin.operative-data-forms.show', compact('form'));
    }

    public function approve(OperativeDataForm $form)
    {
        $this->authorize('update', $form);
        
        $form->update([
            'status' => OperativeDataForm::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('admin.operative-data-forms.index')
            ->with('success', 'Form approved successfully!');
    }

    public function reject(Request $request, OperativeDataForm $form)
    {
        $this->authorize('update', $form);
        
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $form->update([
            'status' => OperativeDataForm::STATUS_REJECTED,
            'rejected_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.operative-data-forms.index')
            ->with('success', 'Form rejected successfully!');
    }

    public function createAccount(Request $request, OperativeDataForm $form)
    {
        $this->authorize('update', $form);
        
        // Check if form is approved and account not already created
        if ($form->status !== OperativeDataForm::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Form must be approved before creating account.');
        }
        
        if ($form->account_created) {
            return redirect()->back()->with('error', 'Account already created for this form.');
        }
        
        $request->validate([
            'temporary_password' => 'required|string|min:8',
        ]);
        
        try {
            \DB::transaction(function () use ($form, $request) {
                // Create User account
                $user = User::create([
                    'name' => $form->full_name,
                    'email' => $form->email_address,
                    'password' => \Hash::make($request->temporary_password),
                    'phone' => $form->mobile_number,
                    'role' => User::ROLE_OPERATIVE,
                    'company_id' => $form->company_id,
                    'is_active' => true,
                ]);
                
                // Split full name into first and last name
                $nameParts = explode(' ', trim($form->full_name), 2);
                $firstName = $nameParts[0] ?? 'Unknown';
                $lastName = $nameParts[1] ?? 'Unknown';
                
                // Generate unique employee ID
                $employeeId = 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
                
                // Create Employee record with all the form data
                $employee = Employee::create([
                    'user_id' => $user->id,
                    'company_id' => $form->company_id,
                    'employee_id' => $employeeId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $form->email_address,
                    'phone' => $form->mobile_number,
                    'address' => $form->home_address,
                    'date_of_birth' => $form->date_of_birth,
                    'country' => 'UK', // Default for UK-based company
                    'role' => 'foreman', // Closest allowed role for operatives
                    'job_title' => $form->primary_trade ?: 'General Operative',
                    'hire_date' => now()->format('Y-m-d'),
                    'employment_status' => 'active',
                    'employment_type' => 'full_time',
                    'salary_type' => 'hourly', // Changed from 'daily' to allowed value
                    'nationality' => $form->nationality,
                    'postcode' => $form->postcode,
                    'emergency_contact_name' => $form->emergency_contact_name,
                    'emergency_contact_relationship' => $form->emergency_contact_relationship,
                    'emergency_contact_phone' => $form->emergency_contact_number,
                    'national_insurance_number' => $form->national_insurance_number,
                    'utr_number' => $form->utr_number,
                    'cscs_card_type' => $form->cscs_card_type,
                    'cscs_card_number' => $form->cscs_card_number,
                    'cscs_card_expiry' => $form->cscs_card_expiry,
                    'right_to_work_uk' => $form->right_to_work_uk,
                    'passport_id_provided' => $form->passport_id_provided,
                    'bank_name' => $form->bank_name,
                    'account_holder_name' => $form->account_holder_name,
                    'sort_code' => $form->sort_code,
                    'account_number' => $form->account_number,
                    'primary_trade' => $form->primary_trade,
                    'years_experience' => $form->years_experience,
                    'other_cards_licenses' => $form->other_cards_licenses,
                    'qualifications' => $form->qualifications_certifications,
                    'cis_status' => 'pending',
                    'cis_applicable' => true,
                    'cis_rate' => 20,
                    'is_active' => true,
                ]);
                
                // Transfer CSCS card images to operative profile documents
                $this->transferCSCSCardImages($form, $employee);
                
                // Update form to mark account as created
                $form->update([
                    'account_created' => true,
                    'account_created_at' => now(),
                    'account_created_by' => auth()->id(),
                    'created_user_id' => $user->id,
                ]);
            });
            
            return redirect()->back()->with('success', 'Operative account created successfully! Login: ' . $form->email_address);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create operative account: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create account. Please try again.');
        }
    }

    // Public methods for operatives to fill forms
    public function showPublicForm($token)
    {
        $form = OperativeDataForm::where('share_token', $token)->firstOrFail();
        
        // If already submitted, show read-only view
        if ($form->submitted_at) {
            return view('operative-data-forms.submitted', compact('form'));
        }
        
        return view('operative-data-forms.form', compact('form'));
    }

    public function submitPublicForm(Request $request, $token)
    {
        $form = OperativeDataForm::where('share_token', $token)->firstOrFail();
        
        // Check if already submitted
        if ($form->submitted_at) {
            return redirect()->route('operative-data-form.show', $token)
                ->with('error', 'This form has already been submitted.');
        }

        $validated = $request->validate([
            // Personal Information
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email_address' => 'required|email|max:255',
            'home_address' => 'required|string',
            'postcode' => 'required|string|max:10',
            
            // Emergency Contact
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_relationship' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:20',
            
            // Work Documentation
            'national_insurance_number' => 'required|string|max:13',
            'utr_number' => 'nullable|string|max:20',
            'cscs_card_type' => 'nullable|string|max:255',
            'cscs_card_number' => 'nullable|string|max:255',
            'cscs_card_expiry' => 'nullable|date|after_or_equal:today',
            'cscs_card_front_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'cscs_card_back_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'right_to_work_uk' => 'required|boolean',
            'passport_id_provided' => 'required|boolean',
            
            // Bank Details
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'sort_code' => 'required|string|size:6',
            'account_number' => 'required|string|max:8',
            
            // Trade and Qualifications
            'primary_trade' => 'required|string|max:255',
            'years_experience' => 'required|integer|min:0|max:50',
            'qualifications_certifications' => 'nullable|string',
            'other_cards_licenses' => 'nullable|string',
            
            // Declaration
            'declaration_confirmed' => 'required|accepted',
        ]);

        // Handle file uploads
        if ($request->hasFile('cscs_card_front_image')) {
            $frontImage = $request->file('cscs_card_front_image');
            $frontPath = $frontImage->store('cscs-cards/' . $form->share_token, 'public');
            $validated['cscs_card_front_image'] = $frontPath;
        }

        if ($request->hasFile('cscs_card_back_image')) {
            $backImage = $request->file('cscs_card_back_image');
            $backPath = $backImage->store('cscs-cards/' . $form->share_token, 'public');
            $validated['cscs_card_back_image'] = $backPath;
        }

        $validated['submitted_at'] = now();
        $validated['status'] = OperativeDataForm::STATUS_PENDING;

        $form->update($validated);

        return redirect()->route('operative-data-form.show', $token)
            ->with('success', 'Your information has been submitted successfully! You will be contacted once it has been reviewed.');
    }

    /**
     * Transfer CSCS card images from form to operative profile documents
     */
    private function transferCSCSCardImages(OperativeDataForm $form, Employee $employee)
    {
        // Check if form has CSCS card images
        if (!$form->cscs_card_front_image && !$form->cscs_card_back_image) {
            return;
        }

        // Create documents directory for this employee if it doesn't exist
        $documentsPath = 'employee-documents/' . $employee->id;
        Storage::disk('public')->makeDirectory($documentsPath);

        // Transfer front image if exists
        if ($form->cscs_card_front_image && Storage::disk('public')->exists($form->cscs_card_front_image)) {
            $this->createCSCSDocumentRecord(
                $form, 
                $employee, 
                $form->cscs_card_front_image, 
                'CSCS Card - Front',
                $documentsPath
            );
        }

        // Transfer back image if exists
        if ($form->cscs_card_back_image && Storage::disk('public')->exists($form->cscs_card_back_image)) {
            $this->createCSCSDocumentRecord(
                $form, 
                $employee, 
                $form->cscs_card_back_image, 
                'CSCS Card - Back',
                $documentsPath
            );
        }
    }

    /**
     * Create a DocumentAttachment record for CSCS card image
     */
    private function createCSCSDocumentRecord(OperativeDataForm $form, Employee $employee, string $sourcePath, string $documentName, string $documentsPath)
    {
        try {
            // Get original file info
            $originalFilename = basename($sourcePath);
            $fileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
            
            // Create new filename for the employee documents
            $newFilename = Str::slug($documentName) . '-' . time() . '.' . $fileExtension;
            $newPath = $documentsPath . '/' . $newFilename;
            
            // Copy file to employee documents location
            Storage::disk('public')->copy($sourcePath, $newPath);
            
            // Get file info
            $fileSize = Storage::disk('public')->size($newPath);
            $mimeType = Storage::disk('public')->mimeType($newPath);
            
            // Determine status based on expiry date
            $status = DocumentAttachment::STATUS_ACTIVE;
            if ($form->cscs_card_expiry) {
                if ($form->cscs_card_expiry->isPast()) {
                    $status = DocumentAttachment::STATUS_EXPIRED;
                } elseif ($form->cscs_card_expiry->diffInDays(now()) <= 30) {
                    $status = DocumentAttachment::STATUS_EXPIRING_SOON;
                }
            }
            
            // Create DocumentAttachment record
            DocumentAttachment::create([
                'company_id' => $form->company_id,
                'attachable_type' => Employee::class,
                'attachable_id' => $employee->id,
                'document_type' => DocumentAttachment::TYPE_CSCS_CARD,
                'document_name' => $documentName,
                'original_filename' => $originalFilename,
                'file_path' => $newPath,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'issue_date' => null, // We don't have issue date from form
                'expiry_date' => $form->cscs_card_expiry,
                'document_number' => $form->cscs_card_number,
                'issuing_authority' => 'CITB', // Default for CSCS cards
                'notes' => 'Automatically transferred from operative data form during account creation',
                'status' => $status,
                'requires_renewal' => true,
                'notification_sent' => false,
                'uploaded_by' => auth()->id() ?: $form->approved_by,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to transfer CSCS card image: ' . $e->getMessage(), [
                'form_id' => $form->id,
                'employee_id' => $employee->id,
                'source_path' => $sourcePath,
                'document_name' => $documentName
            ]);
        }
    }
}