<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.access');
    }

    /**
     * Display a listing of team members
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        // Only show operatives in the Team (now Operatives) section
        $query = User::forCompany()->where('role', User::ROLE_OPERATIVE)
            ->with(['managedProjects', 'tasks', 'employee', 'operativeInvoices']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('certification_status')) {
            $query->whereHas('employee.documentAttachments', function ($q) use ($request) {
                if ($request->certification_status === 'expiring') {
                    $q->where('expiry_date', '<=', now()->addDays(30))
                      ->where('expiry_date', '>=', now());
                } elseif ($request->certification_status === 'expired') {
                    $q->where('expiry_date', '<', now());
                }
            });
        }

        $users = $query->orderBy('name')->paginate(15);

        // Calculate comprehensive metrics
        $metrics = $this->getOperativeMetrics($companyId);

        // Only operative role since this is now the Operatives section
        $roles = [
            'operative' => 'Operative'
        ];

        return view('team.index', compact('users', 'roles', 'metrics'));
    }

    /**
     * Get comprehensive operative metrics
     */
    private function getOperativeMetrics($companyId)
    {
        // Basic operative counts
        $totalOperatives = User::forCompany($companyId)->where('role', User::ROLE_OPERATIVE)->count();
        $activeOperatives = User::forCompany($companyId)->where('role', User::ROLE_OPERATIVE)->where('is_active', true)->count();
        $inactiveOperatives = $totalOperatives - $activeOperatives;

        // CIS related metrics
        $cisApplicableOperatives = Employee::where('company_id', $companyId)
            ->where('cis_applicable', true)->count();
        
        $currentMonth = now()->format('Y-m');
        $currentYear = now()->year;
        
        // CIS payments this month/year
        $cisPaymentsThisMonth = \App\Models\CisPayment::whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('payment_date', 'like', $currentMonth . '%')
            ->sum('cis_deduction');

        $cisPaymentsThisYear = \App\Models\CisPayment::whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereYear('payment_date', $currentYear)
            ->sum('cis_deduction');

        // Invoice metrics
        $pendingInvoices = \App\Models\OperativeInvoice::whereHas('operative', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('status', 'pending')
            ->count();

        $approvedInvoicesThisMonth = \App\Models\OperativeInvoice::whereHas('operative', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('status', 'approved')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('total_amount');

        // Document/Certification metrics
        $expiringCertifications = \App\Models\DocumentAttachment::where('attachable_type', 'App\Models\Employee')
            ->whereHas('attachable', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->count();

        $expiredCertifications = \App\Models\DocumentAttachment::where('attachable_type', 'App\Models\Employee')
            ->whereHas('attachable', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->where('expiry_date', '<', now())
            ->count();

        // Task/Project allocation metrics
        $operativesWithActiveTasks = User::forCompany($companyId)
            ->where('role', User::ROLE_OPERATIVE)
            ->whereHas('tasks', function ($q) {
                $q->whereIn('status', ['pending', 'in_progress']);
            })
            ->count();

        // Performance metrics (last 30 days)
        $completedTasksLast30Days = \App\Models\Task::whereHas('assignedUser', function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->where('role', User::ROLE_OPERATIVE);
            })
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        return [
            // Basic counts
            'total_operatives' => $totalOperatives,
            'active_operatives' => $activeOperatives,
            'inactive_operatives' => $inactiveOperatives,
            'activity_rate' => $totalOperatives > 0 ? round(($activeOperatives / $totalOperatives) * 100, 1) : 0,

            // CIS metrics
            'cis_applicable_operatives' => $cisApplicableOperatives,
            'cis_deductions_this_month' => $cisPaymentsThisMonth,
            'cis_deductions_this_year' => $cisPaymentsThisYear,
            'cis_compliance_rate' => $totalOperatives > 0 ? round(($cisApplicableOperatives / $totalOperatives) * 100, 1) : 0,

            // Invoice metrics
            'pending_invoices' => $pendingInvoices,
            'approved_invoices_amount' => $approvedInvoicesThisMonth,

            // Certification metrics
            'expiring_certifications' => $expiringCertifications,
            'expired_certifications' => $expiredCertifications,
            'certification_compliance' => ($expiringCertifications + $expiredCertifications) === 0 ? 100 : 
                round((1 - (($expiredCertifications) / ($expiringCertifications + $expiredCertifications + 1))) * 100, 1),

            // Work allocation metrics
            'operatives_with_active_tasks' => $operativesWithActiveTasks,
            'utilization_rate' => $activeOperatives > 0 ? round(($operativesWithActiveTasks / $activeOperatives) * 100, 1) : 0,
            'completed_tasks_last_30_days' => $completedTasksLast30Days,

            // Trend indicators (simplified for now)
            'trend_active_operatives' => 'up', // You can implement actual trend calculation
            'trend_cis_deductions' => 'stable',
            'trend_task_completion' => 'up',
        ];
    }

    /**
     * Show the form for creating a new team member
     */
    public function create()
    {
        // Only allow creating operatives in this section
        $roles = [
            'operative' => 'Operative'
        ];

        return view('team.create', compact('roles'));
    }

    /**
     * Store a newly created team member
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:operative',
            'phone' => 'nullable|string|max:20',
            'day_rate' => 'nullable|numeric|min:0',
            'cis_applicable' => 'sometimes|accepted',
            'cis_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = User::create([
            'company_id' => auth()->user()->company_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        // Create Employee record for CIS settings (operatives only)
        if ($request->role === 'operative') {
            $nameParts = explode(' ', $user->name, 2);
            
            Employee::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'employee_id' => 'EMP' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => 'foreman', // Use a valid enum value from Employee model
                'job_title' => 'Operative',
                'hire_date' => now()->toDateString(),
                'employment_status' => 'active',
                'day_rate' => $request->day_rate ?? 0,
                'cis_applicable' => $request->has('cis_applicable'),
                'cis_rate' => $request->cis_rate ?? 0,
            ]);
        }

        return redirect()->route('team.index')
                        ->with('success', 'Operative added successfully.');
    }

    /**
     * Display the specified team member
     */
    public function show(User $team)
    {
        $team->load(['managedProjects', 'tasks', 'createdTasks']);
        return view('team.show', ['member' => $team]);
    }

    /**
     * Show the form for editing the specified team member
     */
    public function edit(User $team)
    {
        // Only allow editing operatives in this section
        if ($team->role !== User::ROLE_OPERATIVE) {
            abort(404, 'User not found in operatives section.');
        }

        $roles = [
            'operative' => 'Operative'
        ];

        // Get or create employee record for this user
        $employee = Employee::where('user_id', $team->id)
            ->where('company_id', $team->company_id)
            ->first();

        return view('team.edit', ['member' => $team, 'employee' => $employee, 'roles' => $roles]);
    }

    /**
     * Update the specified team member
     */
    public function update(Request $request, User $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $team->id,
            'role' => 'required|in:operative',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'sometimes|accepted',
            'day_rate' => 'nullable|numeric|min:0',
            'cis_applicable' => 'sometimes|accepted',
            'cis_rate' => 'nullable|numeric|min:0',
        ]);


        // Update user record
        $team->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        // Handle employee record for CIS settings (only for operatives)
        if ($request->role === 'operative') {
            $employee = Employee::where('user_id', $team->id)
                ->where('company_id', $team->company_id)
                ->first();

            if (!$employee) {
                // Create employee record if it doesn't exist
                $nameParts = explode(' ', $team->name, 2);
                // Generate unique employee ID
                $employeeId = 'EMP' . str_pad($team->id, 4, '0', STR_PAD_LEFT);
                
                $employeeData = [
                    'company_id' => $team->company_id,
                    'user_id' => $team->id,
                    'employee_id' => $employeeId,
                    'first_name' => $nameParts[0] ?? 'Unknown',
                    'last_name' => $nameParts[1] ?? '',
                    'email' => $team->email,
                    'phone' => $team->phone,
                    'role' => 'foreman', // Use a valid enum value from Employee model
                    'job_title' => 'Operative',
                    'hire_date' => now()->toDateString(),
                    'employment_status' => 'active',
                    'is_active' => $team->is_active,
                    'day_rate' => $request->day_rate ?: null,
                    'cis_applicable' => $request->has('cis_applicable'),
                    'cis_rate' => $request->has('cis_applicable') ? ($request->cis_rate ?: 20.00) : null,
                ];
                
                $employee = Employee::create($employeeData);
            } else {
                // Update existing employee record
                $updateData = [
                    'day_rate' => $request->day_rate ?: null,
                    'cis_applicable' => $request->has('cis_applicable'),
                    'cis_rate' => $request->has('cis_applicable') ? ($request->cis_rate ?: 20.00) : null,
                ];
                
                $employee->update($updateData);
            }
        }

        return redirect()->route('team.index')
                        ->with('success', 'Operative updated successfully.');
    }

    /**
     * Remove the specified team member
     */
    public function destroy(User $team)
    {
        // Don't allow deletion of the current user
        if ($team->id === auth()->id()) {
            return redirect()->route('team.index')
                           ->with('error', 'You cannot delete your own account.');
        }

        $team->delete();

        return redirect()->route('team.index')
                        ->with('success', 'Team member removed successfully.');
    }
}
