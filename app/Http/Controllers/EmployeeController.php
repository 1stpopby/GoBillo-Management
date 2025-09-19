<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Site;
use App\Models\EmployeeSiteAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.access');
    }

    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        // Query Users with management roles only (not operatives)
        $query = User::forCompany()->whereIn('role', [
            User::ROLE_COMPANY_ADMIN,
            User::ROLE_PROJECT_MANAGER,
            User::ROLE_SITE_MANAGER,
            User::ROLE_CONTRACTOR
        ])->with(['managedProjects', 'assignedTasks', 'createdTasks']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('project_status')) {
            if ($request->project_status === 'with_projects') {
                $query->whereHas('managedProjects');
            } elseif ($request->project_status === 'without_projects') {
                $query->whereDoesntHave('managedProjects');
            }
        }

        $employees = $query->orderBy('name')->paginate(15);

        // Calculate comprehensive metrics
        $metrics = $this->getEmployeeMetrics($companyId);

        // Get filter options for management roles
        $roles = [
            User::ROLE_COMPANY_ADMIN => 'Company Admin',
            User::ROLE_PROJECT_MANAGER => 'Project Manager',
            User::ROLE_SITE_MANAGER => 'Site Manager',
            User::ROLE_CONTRACTOR => 'Contractor'
        ];
        $sites = Site::forCompany()->orderBy('name')->get();

        return view('employees.index', compact('employees', 'roles', 'sites', 'metrics'));
    }

    /**
     * Get comprehensive employee metrics
     */
    private function getEmployeeMetrics($companyId)
    {
        // Basic employee counts by role
        $totalEmployees = User::forCompany($companyId)->whereIn('role', [
            User::ROLE_COMPANY_ADMIN,
            User::ROLE_PROJECT_MANAGER,
            User::ROLE_SITE_MANAGER,
            User::ROLE_CONTRACTOR
        ])->count();

        $activeEmployees = User::forCompany($companyId)->whereIn('role', [
            User::ROLE_COMPANY_ADMIN,
            User::ROLE_PROJECT_MANAGER,
            User::ROLE_SITE_MANAGER,
            User::ROLE_CONTRACTOR
        ])->where('is_active', true)->count();

        // Role breakdown
        $roleBreakdown = User::forCompany($companyId)->whereIn('role', [
            User::ROLE_COMPANY_ADMIN,
            User::ROLE_PROJECT_MANAGER,
            User::ROLE_SITE_MANAGER,
            User::ROLE_CONTRACTOR
        ])->selectRaw('role, COUNT(*) as count')
        ->groupBy('role')
        ->pluck('count', 'role')
        ->toArray();

        // Project management metrics
        $totalProjects = \App\Models\Project::forCompany($companyId)->count();
        $activeProjects = \App\Models\Project::forCompany($companyId)
            ->whereIn('status', ['planning', 'in_progress'])->count();
        $completedProjectsThisMonth = \App\Models\Project::forCompany($companyId)
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->startOfMonth())
            ->count();

        $employeesWithProjects = User::forCompany($companyId)
            ->whereIn('role', [User::ROLE_PROJECT_MANAGER, User::ROLE_SITE_MANAGER])
            ->whereHas('managedProjects')
            ->count();

        // Task management metrics
        $totalTasks = \App\Models\Task::whereHas('project', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->count();

        $completedTasksThisMonth = \App\Models\Task::whereHas('project', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->startOfMonth())
            ->count();

        $overdueTasks = \App\Models\Task::whereHas('project', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        // CIS management metrics (for employees who might be self-employed)
        $employeesWithCIS = User::forCompany($companyId)
            ->whereIn('role', [User::ROLE_CONTRACTOR])
            ->whereHas('employee', function ($q) {
                $q->where('cis_applicable', true);
            })
            ->count();

        $currentYear = now()->year;
        $cisDeductionsThisYear = \App\Models\CisPayment::whereHas('employee.user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                  ->whereIn('role', [User::ROLE_CONTRACTOR]);
            })
            ->whereYear('payment_date', $currentYear)
            ->sum('cis_deduction');

        // Performance indicators
        $projectSuccessRate = $totalProjects > 0 ? 
            round(((\App\Models\Project::forCompany($companyId)->where('status', 'completed')->count()) / $totalProjects) * 100, 1) : 0;

        $taskCompletionRate = $totalTasks > 0 ? 
            round(((\App\Models\Task::whereHas('project', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->where('status', 'completed')->count()) / $totalTasks) * 100, 1) : 0;

        // Utilization metrics
        $managersWithActiveProjects = User::forCompany($companyId)
            ->whereIn('role', [User::ROLE_PROJECT_MANAGER, User::ROLE_SITE_MANAGER])
            ->whereHas('managedProjects', function ($q) {
                $q->whereIn('status', ['planning', 'in_progress']);
            })
            ->count();

        $totalManagers = User::forCompany($companyId)
            ->whereIn('role', [User::ROLE_PROJECT_MANAGER, User::ROLE_SITE_MANAGER])
            ->count();

        $managerUtilization = $totalManagers > 0 ? 
            round(($managersWithActiveProjects / $totalManagers) * 100, 1) : 0;

        return [
            // Basic counts
            'total_employees' => $totalEmployees,
            'active_employees' => $activeEmployees,
            'inactive_employees' => $totalEmployees - $activeEmployees,
            'activity_rate' => $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100, 1) : 0,

            // Role breakdown
            'role_breakdown' => $roleBreakdown,
            'company_admins' => $roleBreakdown[User::ROLE_COMPANY_ADMIN] ?? 0,
            'project_managers' => $roleBreakdown[User::ROLE_PROJECT_MANAGER] ?? 0,
            'site_managers' => $roleBreakdown[User::ROLE_SITE_MANAGER] ?? 0,
            'contractors' => $roleBreakdown[User::ROLE_CONTRACTOR] ?? 0,

            // Project metrics
            'total_projects' => $totalProjects,
            'active_projects' => $activeProjects,
            'completed_projects_this_month' => $completedProjectsThisMonth,
            'employees_with_projects' => $employeesWithProjects,
            'project_success_rate' => $projectSuccessRate,

            // Task metrics
            'total_tasks' => $totalTasks,
            'completed_tasks_this_month' => $completedTasksThisMonth,
            'overdue_tasks' => $overdueTasks,
            'task_completion_rate' => $taskCompletionRate,

            // CIS metrics
            'employees_with_cis' => $employeesWithCIS,
            'cis_deductions_this_year' => $cisDeductionsThisYear,

            // Utilization metrics
            'manager_utilization' => $managerUtilization,
            'managers_with_active_projects' => $managersWithActiveProjects,

            // Trend indicators (simplified)
            'trend_active_employees' => 'stable',
            'trend_project_completion' => 'up',
            'trend_task_efficiency' => 'up',
        ];
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        $roles = Employee::getRoleOptions();
        $sites = Site::forCompany()->orderBy('name')->get();
        
        return view('employees.create', compact('roles', 'sites'));
    }

    /**
     * Store a newly created employee
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees')->where('company_id', auth()->user()->company_id)
            ],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|size:2',
            'role' => 'required|in:' . implode(',', array_keys(Employee::getRoleOptions())),
            'department' => 'nullable|string|max:255',
            'job_title' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'employment_status' => 'required|in:active,inactive,terminated,on_leave',
            'employment_type' => 'required|in:full_time,part_time,contract,consultant',
            'salary' => 'nullable|numeric|min:0',
            'salary_type' => 'required|in:hourly,monthly,yearly',
            'skills' => 'nullable|string',
            'certifications' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'site_allocations' => 'nullable|array',
            'site_allocations.*.site_id' => 'required|exists:sites,id',
            'site_allocations.*.allocated_from' => 'required|date',
            'site_allocations.*.allocated_until' => 'nullable|date|after:site_allocations.*.allocated_from',
            'site_allocations.*.allocation_type' => 'required|in:primary,secondary,temporary',
            'site_allocations.*.allocation_percentage' => 'required|numeric|min:1|max:100',
            'site_allocations.*.responsibilities' => 'nullable|string',
        ]);

        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Process skills, certifications, qualifications
        $skills = $request->skills ? array_map('trim', explode(',', $request->skills)) : null;
        $certifications = $request->certifications ? array_map('trim', explode(',', $request->certifications)) : null;
        $qualifications = $request->qualifications ? array_map('trim', explode(',', $request->qualifications)) : null;

        // Generate auto-incremented employee ID with transaction safety
        $employee = \DB::transaction(function () use ($request, $avatarPath, $skills, $certifications, $qualifications) {
            $employeeId = $this->generateEmployeeId(auth()->user()->company_id);
            
            return Employee::create([
            'company_id' => auth()->user()->company_id,
            'employee_id' => $employeeId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country ?? 'US',
            'role' => $request->role,
            'department' => $request->department,
            'job_title' => $request->job_title,
            'hire_date' => $request->hire_date,
            'employment_status' => $request->employment_status,
            'employment_type' => $request->employment_type,
            'salary' => $request->salary,
            'salary_type' => $request->salary_type,
            'skills' => $skills,
            'certifications' => $certifications,
            'qualifications' => $qualifications,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'notes' => $request->notes,
            'avatar' => $avatarPath,
            ]);
        });

        // Handle site allocations
        if ($request->has('site_allocations')) {
            foreach ($request->site_allocations as $allocation) {
                if (!empty($allocation['site_id'])) {
                    EmployeeSiteAllocation::create([
                        'employee_id' => $employee->id,
                        'site_id' => $allocation['site_id'],
                        'allocated_from' => $allocation['allocated_from'],
                        'allocated_until' => $allocation['allocated_until'],
                        'allocation_type' => $allocation['allocation_type'],
                        'allocation_percentage' => $allocation['allocation_percentage'],
                        'responsibilities' => $allocation['responsibilities'],
                        'status' => 'active'
                    ]);
                }
            }
        }

        return redirect()->route('employees.show', $employee)
                        ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee
     */
    public function show(Employee $employee)
    {
        $employee->load(['activeSiteAllocations.site', 'user']);
        
        // Load tab data
        $assignedAssets = $employee->assignedAssets()->with(['category', 'location', 'vendor'])->get();
        $documents = $employee->documents()->with(['project', 'task'])->latest()->limit(10)->get();
        $expenses = $employee->expenses()->with(['project'])->latest()->limit(10)->get();
        $invoices = $employee->invoices()->with(['client', 'project'])->latest()->limit(10)->get();
        
        // Financial summary for the employee
        $financialSummary = [
            'total_expenses' => $employee->expenses()->where('status', 'approved')->sum('amount'),
            'pending_expenses' => $employee->expenses()->where('status', 'submitted')->sum('amount'),
            'reimbursed_expenses' => $employee->expenses()->where('status', 'reimbursed')->sum('amount'),
            'billable_expenses' => $employee->expenses()->where('is_billable', true)->sum('amount'),
        ];
        
        // CIS data (Construction Industry Scheme) - real data
        $currentYear = now()->year;
        $cisPayments = $employee->cisPayments()->whereYear('payment_date', $currentYear)->get();
        
        $cisData = [
            'registration_number' => $employee->cis_number ?? null,
            'verification_status' => $employee->cis_status ?? 'pending',
            'deductions_ytd' => $cisPayments->sum('cis_deduction'),
            'gross_payments_ytd' => $cisPayments->sum('gross_amount'),
            'net_payments_ytd' => $cisPayments->sum('net_payment'),
            'payment_count' => $cisPayments->count(),
            'last_payment_date' => $cisPayments->max('payment_date'),
            'average_rate' => $cisPayments->avg('cis_rate') ?? 0,
        ];
        
        return view('employees.show', compact(
            'employee', 
            'assignedAssets', 
            'documents', 
            'expenses', 
            'invoices', 
            'financialSummary',
            'cisData'
        ));
    }

    /**
     * Show the form for editing the specified employee (User model)
     */
    public function editUser(User $user)
    {
        // Ensure the user belongs to the same company
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow editing of management roles (not operatives)
        if ($user->role === User::ROLE_OPERATIVE) {
            return redirect()->route('employees.index')
                           ->with('error', 'Operatives should be managed through the Team section.');
        }

        $roles = [
            User::ROLE_COMPANY_ADMIN => 'Company Admin',
            User::ROLE_PROJECT_MANAGER => 'Project Manager', 
            User::ROLE_SITE_MANAGER => 'Site Manager',
            User::ROLE_CONTRACTOR => 'Contractor'
        ];
        
        return view('employees.edit-user', compact('user', 'roles'));
    }

    /**
     * Update the specified employee (User model)
     */
    public function updateUser(Request $request, User $user)
    {
        // Ensure the user belongs to the same company
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where('company_id', auth()->user()->company_id)->ignore($user->id)
            ],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', [
                User::ROLE_COMPANY_ADMIN,
                User::ROLE_PROJECT_MANAGER,
                User::ROLE_SITE_MANAGER,
                User::ROLE_CONTRACTOR
            ]),
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        return redirect()->route('employees.index')
                        ->with('success', 'Employee updated successfully.');
    }

    /**
     * Delete the specified employee (User model)
     */
    public function deleteUser(User $user)
    {
        // Ensure the user belongs to the same company
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access.');
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('employees.index')
                           ->with('error', 'You cannot delete your own account.');
        }

        // Only allow deleting of management roles (not operatives)
        if ($user->role === User::ROLE_OPERATIVE) {
            return redirect()->route('employees.index')
                           ->with('error', 'Operatives should be managed through the Team section.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('employees.index')
                        ->with('success', "Employee {$userName} has been deleted successfully.");
    }

    /**
     * Show the form for editing the specified employee (Employee model - for operatives)
     */
    public function edit(Employee $employee)
    {
        $employee->load(['activeSiteAllocations.site']);
        $roles = Employee::getRoleOptions();
        $sites = Site::forCompany()->orderBy('name')->get();
        
        return view('employees.edit', compact('employee', 'roles', 'sites'));
    }

    /**
     * Update the specified employee
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'employee_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees')->where('company_id', auth()->user()->company_id)->ignore($employee->id)
            ],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees')->where('company_id', auth()->user()->company_id)->ignore($employee->id)
            ],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|size:2',
            'role' => 'required|in:' . implode(',', array_keys(Employee::getRoleOptions())),
            'department' => 'nullable|string|max:255',
            'job_title' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date|after:hire_date',
            'employment_status' => 'required|in:active,inactive,terminated,on_leave',
            'employment_type' => 'required|in:full_time,part_time,contract,consultant',
            'salary' => 'nullable|numeric|min:0',
            'salary_type' => 'required|in:hourly,monthly,yearly',
            'primary_trade' => 'nullable|string|max:255',
            'years_experience' => 'nullable|integer|min:0|max:50',
            'skills' => 'nullable|string',
            'certifications' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'other_cards_licenses' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            // Work Documentation
            'national_insurance_number' => 'nullable|string|max:20',
            'utr_number' => 'nullable|string|max:20',
            'cscs_card_type' => 'nullable|string|max:255',
            'cscs_card_number' => 'nullable|string|max:50',
            'cscs_card_expiry' => 'nullable|date|after:today',
            'right_to_work_uk' => 'nullable|boolean',
            'passport_id_provided' => 'nullable|boolean',
            // Bank Details
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'sort_code' => 'nullable|string|max:8',
            'account_number' => 'nullable|string|max:8',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle avatar upload
        $avatarPath = $employee->avatar;
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Process skills, certifications, qualifications, and other cards/licenses
        $skills = $request->skills ? array_map('trim', explode(',', $request->skills)) : null;
        $certifications = $request->certifications ? array_map('trim', explode(',', $request->certifications)) : null;
        $qualifications = $request->qualifications ? array_map('trim', explode(',', $request->qualifications)) : null;
        $otherCardsLicenses = $request->other_cards_licenses ? array_map('trim', explode(',', $request->other_cards_licenses)) : null;

        $employee->update([
            'employee_id' => $request->employee_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'nationality' => $request->nationality,
            'gender' => $request->gender,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'postcode' => $request->postcode,
            'country' => $request->country ?? 'GB',
            'role' => $request->role,
            'department' => $request->department,
            'job_title' => $request->job_title,
            'hire_date' => $request->hire_date,
            'termination_date' => $request->termination_date,
            'employment_status' => $request->employment_status,
            'employment_type' => $request->employment_type,
            'salary' => $request->salary,
            'salary_type' => $request->salary_type,
            'primary_trade' => $request->primary_trade,
            'years_experience' => $request->years_experience,
            'skills' => $skills,
            'certifications' => $certifications,
            'qualifications' => $qualifications,
            'other_cards_licenses' => $otherCardsLicenses,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            // Work Documentation
            'national_insurance_number' => $request->national_insurance_number,
            'utr_number' => $request->utr_number,
            'cscs_card_type' => $request->cscs_card_type,
            'cscs_card_number' => $request->cscs_card_number,
            'cscs_card_expiry' => $request->cscs_card_expiry,
            'right_to_work_uk' => $request->boolean('right_to_work_uk'),
            'passport_id_provided' => $request->boolean('passport_id_provided'),
            // Bank Details
            'bank_name' => $request->bank_name,
            'account_holder_name' => $request->account_holder_name,
            'sort_code' => $request->sort_code,
            'account_number' => $request->account_number,
            'notes' => $request->notes,
            'avatar' => $avatarPath,
        ]);

        return redirect()->route('employees.show', $employee)
                        ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee
     */
    public function destroy(Employee $employee)
    {
        // Delete avatar if exists
        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }

        $employee->delete();

        return redirect()->route('employees.index')
                        ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Allocate employee to site
     */
    public function allocateToSite(Request $request, Employee $employee)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'allocated_from' => 'required|date',
            'allocated_until' => 'nullable|date|after:allocated_from',
            'allocation_type' => 'required|in:primary,secondary,temporary',
            'allocation_percentage' => 'required|numeric|min:1|max:100',
            'responsibilities' => 'nullable|string',
        ]);

        // Verify site belongs to same company
        $site = Site::forCompany()->findOrFail($request->site_id);

        EmployeeSiteAllocation::create([
            'employee_id' => $employee->id,
            'site_id' => $site->id,
            'allocated_from' => $request->allocated_from,
            'allocated_until' => $request->allocated_until,
            'allocation_type' => $request->allocation_type,
            'allocation_percentage' => $request->allocation_percentage,
            'responsibilities' => $request->responsibilities,
            'status' => 'active'
        ]);

        return redirect()->route('employees.show', $employee)
                        ->with('success', 'Employee allocated to site successfully.');
    }

    /**
     * Remove employee from site
     */
    public function removeFromSite(Employee $employee, EmployeeSiteAllocation $allocation)
    {
        $allocation->update(['status' => 'completed']);

        return redirect()->route('employees.show', $employee)
                        ->with('success', 'Employee removed from site successfully.');
    }

    /**
     * Update CIS information for employee
     */
    public function updateCis(Request $request, Employee $employee)
    {
        $request->validate([
            'cis_number' => 'nullable|string|max:50',
            'cis_status' => 'required|in:pending,verified,rejected,not_registered',
        ]);

        $employee->update([
            'cis_number' => $request->cis_number,
            'cis_status' => $request->cis_status,
        ]);

        return redirect()->route('employees.show', $employee)
                        ->with('success', 'CIS information updated successfully.');
    }

    /**
     * Generate auto-incremented employee ID for the company
     * Format: EMP-[CompanyID]-[SequentialNumber] (e.g., EMP-01-001, EMP-01-002)
     */
    private function generateEmployeeId($companyId)
    {
        // Use PostgreSQL UPSERT to atomically get next sequence number
        $result = \DB::select("
            INSERT INTO employee_counters (company_id, last_sequence, created_at, updated_at) 
            VALUES (?, 1, NOW(), NOW()) 
            ON CONFLICT (company_id) 
            DO UPDATE SET 
                last_sequence = employee_counters.last_sequence + 1,
                updated_at = NOW()
            RETURNING last_sequence
        ", [$companyId]);
        
        $nextNumber = $result[0]->last_sequence;
        
        // Format: EMP-[CompanyID]-[SequentialNumber] (e.g., EMP-1-001, EMP-15-002)
        $sequentialNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return "EMP-{$companyId}-{$sequentialNumber}";
    }
}
