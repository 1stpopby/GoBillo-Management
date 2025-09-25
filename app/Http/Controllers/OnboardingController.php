<?php

namespace App\Http\Controllers;

use App\Models\OnboardingStep;
use App\Models\CompanyOnboardingProgress;
use App\Models\UserOnboardingState;
use App\Models\Company;
use App\Models\Client;
use App\Models\User;
use App\Models\Employee;
use App\Models\Site;
use App\Models\Project;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    /**
     * Get onboarding status for current user
     */
    public function status()
    {
        $user = auth()->user();
        
        if (!$user || !$user->company_id) {
            return response()->json(['show' => false]);
        }
        
        // Get or create user onboarding state
        $userState = UserOnboardingState::firstOrCreate(
            ['user_id' => $user->id],
            ['has_seen_welcome' => false]
        );
        
        // Check if onboarding should be shown
        if (!$userState->shouldShowOnboarding()) {
            return response()->json(['show' => false]);
        }
        
        // Get or create company onboarding progress
        $companyProgress = CompanyOnboardingProgress::firstOrCreate(
            ['company_id' => $user->company_id],
            [
                'steps' => [],
                'current_step' => 'company_profile',
                'completed_steps' => 0,
                'total_steps' => 6,
                'started_at' => now()
            ]
        );
        
        // Update progress based on actual data
        $this->updateCompanyProgress($companyProgress);
        
        // Get onboarding steps
        $steps = OnboardingStep::active()->with('helpArticle')->get();
        
        return response()->json([
            'show' => true,
            'steps' => $steps,
            'progress' => $companyProgress,
            'userState' => $userState,
            'completionPercentage' => $companyProgress->completion_percentage
        ]);
    }
    
    /**
     * Get detailed onboarding data for dashboard
     */
    public function getDashboardData()
    {
        $user = auth()->user();
        
        if (!$user || !$user->company_id) {
            return null;
        }
        
        // Get or create user onboarding state
        $userState = UserOnboardingState::firstOrCreate(
            ['user_id' => $user->id],
            ['has_seen_welcome' => false]
        );
        
        // Don't show if user has dismissed or completed
        if (!$userState->shouldShowOnboarding()) {
            return null;
        }
        
        // Get or create company onboarding progress
        $companyProgress = CompanyOnboardingProgress::firstOrCreate(
            ['company_id' => $user->company_id],
            [
                'steps' => [],
                'current_step' => 'company_profile',
                'completed_steps' => 0,
                'total_steps' => 6,
                'started_at' => now()
            ]
        );
        
        // Update progress based on actual data
        $this->updateCompanyProgress($companyProgress);
        
        // Get onboarding steps
        $steps = OnboardingStep::active()->with('helpArticle')->get();
        
        // Mark user as having seen the welcome
        if (!$userState->has_seen_welcome) {
            $userState->update(['has_seen_welcome' => true, 'last_seen_at' => now()]);
        }
        
        return [
            'show' => true,
            'steps' => $steps,
            'progress' => $companyProgress,
            'userState' => $userState,
            'completionPercentage' => $companyProgress->completion_percentage,
            'currentStep' => $this->getCurrentStep($companyProgress),
            'nextAction' => $this->getNextAction($companyProgress)
        ];
    }
    
    /**
     * Update company onboarding progress based on actual data
     */
    private function updateCompanyProgress($progress)
    {
        $company = Company::find($progress->company_id);
        if (!$company) {
            return;
        }
        
        $steps = $progress->steps ?? [];
        
        // Step 1: Company Profile
        if ($this->isCompanyProfileComplete($company)) {
            if (!isset($steps['company_profile']) || !$steps['company_profile']['completed']) {
                $progress->markStepCompleted('company_profile');
                $steps = $progress->steps;
            }
        }
        
        // Step 2: First Client
        if (Client::where('company_id', $company->id)->exists()) {
            if (!isset($steps['first_client']) || !$steps['first_client']['completed']) {
                $progress->markStepCompleted('first_client');
                $steps = $progress->steps;
            }
        }
        
        // Step 3: First Manager
        if (User::where('company_id', $company->id)
            ->where('role', 'project_manager')
            ->exists()) {
            if (!isset($steps['first_manager']) || !$steps['first_manager']['completed']) {
                $progress->markStepCompleted('first_manager');
                $steps = $progress->steps;
            }
        }
        
        // Step 4: Operatives
        if (Employee::where('company_id', $company->id)->exists()) {
            if (!isset($steps['operatives']) || !$steps['operatives']['completed']) {
                $progress->markStepCompleted('operatives');
                $steps = $progress->steps;
            }
        }
        
        // Step 5: First Site
        if (Site::where('company_id', $company->id)->exists()) {
            if (!isset($steps['first_site']) || !$steps['first_site']['completed']) {
                $progress->markStepCompleted('first_site');
                $steps = $progress->steps;
            }
        }
        
        // Step 6: First Project
        if (Project::where('company_id', $company->id)->exists()) {
            if (!isset($steps['first_project']) || !$steps['first_project']['completed']) {
                $progress->markStepCompleted('first_project');
                $steps = $progress->steps;
            }
        }
        
        // Update current step
        $progress->current_step = $this->determineCurrentStep($progress);
        $progress->save();
    }
    
    /**
     * Check if company profile is complete
     */
    private function isCompanyProfileComplete($company)
    {
        return !empty($company->name) &&
               !empty($company->address) &&
               !empty($company->phone) &&
               !empty($company->email);
    }
    
    /**
     * Determine the current step based on progress
     */
    private function determineCurrentStep($progress)
    {
        $stepOrder = [
            'company_profile',
            'first_client',
            'first_manager',
            'operatives',
            'first_site',
            'first_project'
        ];
        
        $steps = $progress->steps ?? [];
        
        foreach ($stepOrder as $stepKey) {
            if (!isset($steps[$stepKey]) || !$steps[$stepKey]['completed']) {
                return $stepKey;
            }
        }
        
        return 'completed';
    }
    
    /**
     * Get the current step details
     */
    private function getCurrentStep($progress)
    {
        $currentStepKey = $progress->current_step;
        
        if ($currentStepKey === 'completed') {
            return null;
        }
        
        $step = OnboardingStep::where('key', $currentStepKey)->first();
        
        if ($step) {
            return [
                'key' => $step->key,
                'name' => $step->name,
                'description' => $step->description,
                'route' => $step->route,
                'icon' => $step->icon
            ];
        }
        
        return null;
    }
    
    /**
     * Get the next action for the user
     */
    private function getNextAction($progress)
    {
        $actions = [
            'company_profile' => [
                'text' => 'Complete Company Profile',
                'route' => 'settings',
                'icon' => 'bi-building'
            ],
            'first_client' => [
                'text' => 'Add Your First Client',
                'route' => 'clients.create',
                'icon' => 'bi-person-plus'
            ],
            'first_manager' => [
                'text' => 'Create Manager Account',
                'route' => 'users.create',
                'icon' => 'bi-person-badge'
            ],
            'operatives' => [
                'text' => 'Add Operatives',
                'route' => 'employees.create',
                'icon' => 'bi-people'
            ],
            'first_site' => [
                'text' => 'Create Your First Site',
                'route' => 'sites.create',
                'icon' => 'bi-geo-alt'
            ],
            'first_project' => [
                'text' => 'Set Up First Project',
                'route' => 'projects.create',
                'icon' => 'bi-kanban'
            ]
        ];
        
        return $actions[$progress->current_step] ?? null;
    }
    
    /**
     * Dismiss onboarding temporarily
     */
    public function dismiss()
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userState = UserOnboardingState::where('user_id', $user->id)->first();
        
        if ($userState) {
            $userState->dismiss();
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Skip onboarding permanently
     */
    public function skip()
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userState = UserOnboardingState::where('user_id', $user->id)->first();
        
        if ($userState) {
            $userState->skip();
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Complete onboarding
     */
    public function complete()
    {
        $user = auth()->user();
        
        if (!$user || !$user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Mark user onboarding as complete
        $userState = UserOnboardingState::where('user_id', $user->id)->first();
        if ($userState) {
            $userState->complete();
        }
        
        // Mark company onboarding as complete
        $companyProgress = CompanyOnboardingProgress::where('company_id', $user->company_id)->first();
        if ($companyProgress && !$companyProgress->completed_at) {
            $companyProgress->update(['completed_at' => now()]);
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Manually mark a step as complete (for admin testing)
     */
    public function markStepComplete(Request $request)
    {
        $user = auth()->user();
        
        if (!$user || !$user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $request->validate([
            'step' => 'required|string'
        ]);
        
        $companyProgress = CompanyOnboardingProgress::where('company_id', $user->company_id)->first();
        
        if ($companyProgress) {
            $companyProgress->markStepCompleted($request->step);
        }
        
        return response()->json(['success' => true, 'progress' => $companyProgress]);
    }
}