<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyRegistrationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\SiteContentController;
use App\Http\Controllers\SuperAdmin\FooterLinksController;
use App\Http\Controllers\SuperAdmin\PagesController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CompanyAssignmentController;
use App\Http\Controllers\SuperAdmin\ModuleController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\HireController;
use App\Http\Controllers\ToolHireController;
use App\Http\Controllers\FinancialReportsController;
use App\Http\Controllers\ProjectExpenseController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectVariationController;
use App\Http\Controllers\ProjectSnaggingController;
use App\Http\Controllers\ProjectScheduleController;
use App\Http\Controllers\HealthSafetyController;
use App\Http\Controllers\CisController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FieldOperationsController;
use App\Http\Controllers\TimeTrackingController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Production deployment test endpoint - v1.2
Route::get('/deployment-test', function () {
    return response()->json([
        'status' => 'LIVE',
        'version' => 'v1.2-FIXED',
        'timestamp' => now()->toIsoString(),
        'cache_cleared' => true,
        'message' => 'Production deployment successfully updated!'
    ]);
});

// Company Registration Routes (Public)
Route::get('/get-started', [CompanyRegistrationController::class, 'showGetStarted'])->name('get-started');
Route::post('/company/register', [CompanyRegistrationController::class, 'register'])->name('company.register');
Route::get('/company/welcome', [CompanyRegistrationController::class, 'showWelcome'])->middleware('auth')->name('company.welcome');
Route::post('/company/onboarding', [CompanyRegistrationController::class, 'completeOnboarding'])->middleware('auth')->name('company.onboarding');

// Redirect old register route to our professional get started page
Route::get('/register', function () {
    return redirect()->route('get-started');
});

// Public Pages Routes
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

// Authentication routes (excluding register - we have our own)
Auth::routes(['register' => false]);

// Role-based redirect for /home
Route::get('/home', function () {
    $user = auth()->user();
    
    if (!$user) {
        return redirect('/login');
    }

    // Role-based dashboard redirection
    switch ($user->role) {
        case 'operative':
            return redirect('/operative-dashboard');
        case 'client':
            // Future: could redirect to client portal
            return redirect('/dashboard');
        case 'subcontractor':
            // Future: could redirect to subcontractor portal
            return redirect('/dashboard');
        default:
            // Admin, project managers, contractors go to main dashboard
            return redirect('/dashboard');
    }
})->middleware('auth');

// Blocked user page (with logout only)
Route::middleware(['auth'])->get('/blocked', function () {
    return view('blocked-user');
})->name('blocked');

// Company Assignment Routes (for users without companies)
Route::middleware(['auth'])->group(function () {
    Route::get('/company-assignment', [CompanyAssignmentController::class, 'show'])->name('company.assignment');
    Route::post('/company-assignment', [CompanyAssignmentController::class, 'assign'])->name('company.assign');
});

// SuperAdmin Routes (no company restriction)
Route::middleware(['auth'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::resource('companies', CompanyController::class);
    Route::post('companies/{company}/suspend', [CompanyController::class, 'suspend'])->name('companies.suspend');
    Route::post('companies/{company}/activate', [CompanyController::class, 'activate'])->name('companies.activate');
    
    // Membership Plans Management
    Route::resource('plans', \App\Http\Controllers\SuperAdmin\MembershipPlanController::class);
    Route::post('plans/{plan}/toggle-status', [\App\Http\Controllers\SuperAdmin\MembershipPlanController::class, 'toggleStatus'])->name('plans.toggle-status');
    
    // Subscriptions Management
    Route::resource('subscriptions', \App\Http\Controllers\SuperAdmin\SubscriptionController::class);
    Route::post('subscriptions/{subscription}/cancel', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('subscriptions/{subscription}/reactivate', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'reactivate'])->name('subscriptions.reactivate');
    Route::post('subscriptions/{subscription}/suspend', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
    
    // Settings Management
    Route::get('settings', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/test-stripe', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'testStripe'])->name('settings.test-stripe');
    Route::post('settings/test-google', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'testGoogle'])->name('settings.test-google');
    
    // Email Settings Management
    Route::get('email-settings', [\App\Http\Controllers\SuperAdmin\EmailSettingsController::class, 'index'])->name('email-settings.index');
    Route::post('email-settings', [\App\Http\Controllers\SuperAdmin\EmailSettingsController::class, 'store'])->name('email-settings.store');
    Route::post('email-settings/test', [\App\Http\Controllers\SuperAdmin\EmailSettingsController::class, 'test'])->name('email-settings.test');
    Route::get('email-settings/usage', [\App\Http\Controllers\SuperAdmin\EmailSettingsController::class, 'usage'])->name('email-settings.usage');
    Route::get('email-settings/companies', [\App\Http\Controllers\SuperAdmin\EmailSettingsController::class, 'companyOverview'])->name('email-settings.companies');
    Route::post('email-settings/preview', [\App\Http\Controllers\SuperAdmin\EmailSettingsController::class, 'preview'])->name('email-settings.preview');
    
    // Analytics (placeholder)
    Route::get('analytics', function() {
        return view('superadmin.analytics.index');
    })->name('analytics.index');
    
    // Module Management Routes
    Route::resource('modules', ModuleController::class);
    Route::get('modules/{module}/companies', [ModuleController::class, 'companies'])->name('modules.companies');
    Route::post('modules/{module}/enable', [ModuleController::class, 'enableForCompany'])->name('modules.enable');
    Route::post('modules/{module}/disable', [ModuleController::class, 'disableForCompany'])->name('modules.disable');
    Route::post('modules/bulk-update', [ModuleController::class, 'bulkUpdate'])->name('modules.bulk-update');
    
    // Site Content Management
    Route::get('/site-content', [SiteContentController::class, 'index'])->name('site-content.index');
    Route::post('/site-content/update', [SiteContentController::class, 'update'])->name('site-content.update');
    Route::post('/site-content/initialize', [SiteContentController::class, 'initializeDefaults'])->name('site-content.initialize');
    Route::post('/site-content/reset', [SiteContentController::class, 'resetToDefaults'])->name('site-content.reset');
    
    // Footer Links Management
    Route::get('/footer-links', [FooterLinksController::class, 'index'])->name('footer-links.index');
    Route::post('/footer-links', [FooterLinksController::class, 'store'])->name('footer-links.store');
    Route::put('/footer-links/{footerLink}', [FooterLinksController::class, 'update'])->name('footer-links.update');
    Route::delete('/footer-links/{footerLink}', [FooterLinksController::class, 'destroy'])->name('footer-links.destroy');
    Route::post('/footer-links/initialize', [FooterLinksController::class, 'initializeDefaults'])->name('footer-links.initialize');
    Route::post('/footer-links/update-order', [FooterLinksController::class, 'updateOrder'])->name('footer-links.update-order');
    
    // Pages Management
    Route::resource('pages', PagesController::class);
    Route::post('/pages/initialize', [PagesController::class, 'initializeDefaults'])->name('pages.initialize');
});

// Protected Routes with Company Access Control
Route::middleware(['auth', 'company.access'])->group(function () {
    
    // Core Features
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/operative-dashboard', [\App\Http\Controllers\OperativeDashboardController::class, 'index'])->name('operative-dashboard');
    
    // Operative Time Tracking
    Route::post('/operative/clock-in', [\App\Http\Controllers\OperativeDashboardController::class, 'clockIn'])->name('operative.clock-in');
    Route::post('/operative/clock-out', [\App\Http\Controllers\OperativeDashboardController::class, 'clockOut'])->name('operative.clock-out');
    Route::get('/operative/time-status', [\App\Http\Controllers\OperativeDashboardController::class, 'getTimeStatus'])->name('operative.time-status');
    
    // Operative Invoices
    Route::resource('operative-invoices', \App\Http\Controllers\OperativeInvoiceController::class);
    Route::post('operative-invoices/{operativeInvoice}/submit', [\App\Http\Controllers\OperativeInvoiceController::class, 'submit'])->name('operative-invoices.submit');
    Route::get('ajax/sites-for-manager', [\App\Http\Controllers\OperativeInvoiceController::class, 'getSitesForManager'])->name('ajax.sites-for-manager');
    Route::get('ajax/managers-for-site', [\App\Http\Controllers\ProjectController::class, 'getManagersForSite'])->name('ajax.managers-for-site');
    Route::get('ajax/projects-for-site', [\App\Http\Controllers\HireController::class, 'getProjectsForSite'])->name('ajax.projects-for-site');
    
    // Sites Management
    Route::resource('sites', SiteController::class);
    Route::post('sites/{site}/archive', [SiteController::class, 'archive'])->name('sites.archive');
    Route::post('sites/{site}/unarchive', [SiteController::class, 'unarchive'])->name('sites.unarchive');
    Route::post('sites/bulk-archive-completed', [SiteController::class, 'bulkArchiveCompleted'])->name('sites.bulk-archive-completed');
    
    // Manager Sites - Sites allocated to the manager 
    Route::get('manager/sites', [SiteController::class, 'managerSites'])->name('manager.sites.index');
    Route::get('manager/sites/{site}', [SiteController::class, 'managerShow'])->name('manager.sites.show');
    Route::get('manager/sites/{site}/edit', [SiteController::class, 'managerEdit'])->name('manager.sites.edit');
    Route::put('manager/sites/{site}', [SiteController::class, 'managerUpdate'])->name('manager.sites.update');
    
    // Projects Management  
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/users', [ProjectController::class, 'addUser'])->name('projects.add-user');
    Route::delete('projects/{project}/users/{user}', [ProjectController::class, 'removeUser'])->name('projects.remove-user');
    Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');
    Route::get('projects/{project}/status/{status}', [ProjectController::class, 'updateStatusGet'])->name('projects.update-status-get');
    Route::post('projects/{project}/archive', [ProjectController::class, 'archive'])->name('projects.archive');
    Route::post('projects/{project}/unarchive', [ProjectController::class, 'unarchive'])->name('projects.unarchive');
    Route::post('projects/bulk-archive-completed', [ProjectController::class, 'bulkArchiveCompleted'])->name('projects.bulk-archive-completed');

    // Project Expenses
    Route::prefix('projects/{project}')->name('project.')->group(function () {
        Route::resource('expenses', ProjectExpenseController::class);
        Route::patch('expenses/{expense}/approve', [ProjectExpenseController::class, 'approve'])->name('expenses.approve');
        Route::patch('expenses/{expense}/reject', [ProjectExpenseController::class, 'reject'])->name('expenses.reject');
    });

    // Project Documents
    Route::prefix('projects/{project}')->name('project.')->group(function () {
        Route::resource('documents', ProjectDocumentController::class);
        Route::get('documents/{document}/download', [ProjectDocumentController::class, 'download'])->name('documents.download');
        Route::get('documents/{document}/view', [ProjectDocumentController::class, 'view'])->name('documents.view');
        Route::post('documents/{document}/version', [ProjectDocumentController::class, 'newVersion'])->name('documents.version');
    });

    // Project Variations
    Route::prefix('projects/{project}')->name('project.')->group(function () {
        Route::resource('variations', ProjectVariationController::class);
        Route::patch('variations/{variation}/approve', [ProjectVariationController::class, 'approve'])->name('variations.approve');
        Route::patch('variations/{variation}/reject', [ProjectVariationController::class, 'reject'])->name('variations.reject');
        Route::patch('variations/{variation}/implement', [ProjectVariationController::class, 'implement'])->name('variations.implement');
        Route::post('variations/{variation}/send-email', [ProjectVariationController::class, 'sendEmail'])->name('variations.send-email');
        Route::get('variations/{variation}/email-preview', [ProjectVariationController::class, 'previewEmail'])->name('variations.email-preview');
        Route::get('variations/{variation}/pdf', [ProjectVariationController::class, 'generatePDF'])->name('variations.pdf');
    });

    // Project Snagging
    Route::prefix('projects/{project}')->name('project.')->group(function () {
        Route::resource('snagging', ProjectSnaggingController::class);
        Route::patch('snagging/{snagging}/resolve', [ProjectSnaggingController::class, 'resolve'])->name('snagging.resolve');
        Route::patch('snagging/{snagging}/close', [ProjectSnaggingController::class, 'close'])->name('snagging.close');
    });

    // Tasks Management
    Route::resource('tasks', TaskController::class);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::get('tasks/{task}/status/{status}', [TaskController::class, 'updateStatusGet'])->name('tasks.update-status-get');
    Route::get('tasks/{task}/attachments', [TaskController::class, 'getAttachments'])->name('tasks.attachments');
    Route::get('tasks/{task}/delays', [TaskController::class, 'getDelays'])->name('tasks.delays');
    
    // Task Delay and On Hold Management
    Route::post('tasks/{task}/apply-delay', [TaskController::class, 'applyDelay'])->name('tasks.apply-delay');
    Route::delete('tasks/{task}/remove-delay', [TaskController::class, 'removeDelay'])->name('tasks.remove-delay');
    Route::post('tasks/{task}/apply-on-hold', [TaskController::class, 'applyOnHold'])->name('tasks.apply-on-hold');
    Route::delete('tasks/{task}/remove-on-hold', [TaskController::class, 'removeOnHold'])->name('tasks.remove-on-hold');

    // Task Categories Management
    Route::resource('task-categories', TaskCategoryController::class);
    Route::post('task-categories/{taskCategory}/toggle', [TaskCategoryController::class, 'toggle'])->name('task-categories.toggle');
    Route::post('task-categories/reorder', [TaskCategoryController::class, 'reorder'])->name('task-categories.reorder');

    // Clients Management
    Route::resource('clients', ClientController::class);

    // Documents Management
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    
    // Team Management
    Route::resource('team', TeamController::class);

    // Hire Management
    Route::resource('hire', HireController::class);
    Route::post('hire/{hireRequest}/approve', [HireController::class, 'approve'])->name('hire.approve');
    Route::post('hire/{hireRequest}/reject', [HireController::class, 'reject'])->name('hire.reject');
    Route::post('hire/{hireRequest}/mark-filled', [HireController::class, 'markFilled'])->name('hire.mark-filled');
    Route::get('ajax/projects-for-site-hire', [HireController::class, 'getProjectsForSite'])->name('ajax.projects-for-site-hire');

    // Tool Hire Management
    Route::resource('tool-hire', ToolHireController::class)->parameters(['tool-hire' => 'toolHireRequest']);
    Route::post('tool-hire/{toolHireRequest}/approve', [ToolHireController::class, 'approve'])->name('tool-hire.approve');
    Route::post('tool-hire/{toolHireRequest}/reject', [ToolHireController::class, 'reject'])->name('tool-hire.reject');
    
    // Assets Management
    require __DIR__.'/assets.php';
    
    // Employee Management
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/allocate-site', [EmployeeController::class, 'allocateToSite'])->name('employees.allocate-site');
    Route::delete('employees/{employee}/site-allocations/{allocation}', [EmployeeController::class, 'removeFromSite'])->name('employees.remove-from-site');
    Route::patch('employees/{employee}/cis', [EmployeeController::class, 'updateCis'])->name('employees.update-cis');
    
    // User-based employee management (for managers/staff)
    Route::get('employees/user/{user}/edit', [EmployeeController::class, 'editUser'])->name('employees.edit-user');
    Route::put('employees/user/{user}', [EmployeeController::class, 'updateUser'])->name('employees.update-user');
    Route::delete('employees/user/{user}', [EmployeeController::class, 'deleteUser'])->name('employees.delete-user');

    // CIS Management
    Route::prefix('cis')->name('cis.')->group(function () {
        Route::get('/', [CisController::class, 'index'])->name('index');
        
        // Payments
        Route::get('payments', [CisController::class, 'payments'])->name('payments');
        Route::get('payments/create', [CisController::class, 'createPayment'])->name('payments.create');
        Route::post('payments', [CisController::class, 'storePayment'])->name('payments.store');
        Route::get('payments/{payment}', [CisController::class, 'showPayment'])->name('payments.show');
        Route::post('payments/{payment}/verify', [CisController::class, 'verifyPayment'])->name('payments.verify');
        
        // Returns
        Route::get('returns', [CisController::class, 'returns'])->name('returns');
        Route::post('returns', [CisController::class, 'createReturn'])->name('returns.create');
        Route::get('returns/{cisReturn}', [CisController::class, 'showReturn'])->name('returns.show');
        Route::post('returns/{cisReturn}/submit', [CisController::class, 'submitReturn'])->name('returns.submit');
        Route::get('returns/{cisReturn}/report', [CisController::class, 'generateReturnReport'])->name('returns.report');
        
        // Employee summary
        Route::get('employees/{employee}/summary', [CisController::class, 'getEmployeeSummary'])->name('employees.summary');
        
        // Operative payments page
        Route::get('operative/{employee}/payments', [CisController::class, 'operativePayments'])->name('operative-payments');
        
        // CIS Statement generation
        Route::get('operative/{employee}/statement', [CisController::class, 'generateCisStatement'])->name('operative-statement');
        
        // Employee/Manager CIS payments page
        Route::get('employee/{user}/payments', [CisController::class, 'employeePayments'])->name('employee-payments');
        
    // Employee/Manager CIS Statement generation
    Route::get('employee/{user}/statement', [CisController::class, 'generateEmployeeCisStatement'])->name('employee-statement');
    });

    // Profile Management
    Route::prefix('profiles')->name('profiles.')->group(function () {
        Route::get('operative/{employee}', [ProfileController::class, 'showOperative'])->name('operative');
        Route::get('employee/{user}', [ProfileController::class, 'showEmployee'])->name('employee');
        
        // Document Management
        Route::post('documents/upload', [ProfileController::class, 'uploadDocument'])->name('documents.upload');
        Route::get('documents/{document}/download', [ProfileController::class, 'downloadDocument'])->name('documents.download');
        Route::delete('documents/{document}', [ProfileController::class, 'deleteDocument'])->name('documents.delete');
    });

    // Invoice Management
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
    
    // Operative Invoice Management (for managers)
    Route::post('invoices/operative/{invoice}/approve', [InvoiceController::class, 'approveOperativeInvoice'])->name('invoices.operative.approve');
    Route::post('invoices/operative/{invoice}/reject', [InvoiceController::class, 'rejectOperativeInvoice'])->name('invoices.operative.reject');
    Route::get('invoices/operative/{invoice}/show', [InvoiceController::class, 'showOperativeInvoice'])->name('invoices.operative.show');
    Route::get('invoices/operative/{invoice}/pdf', [InvoiceController::class, 'operativeInvoicePdf'])->name('invoices.operative.pdf');
    
    // Admin Operative Invoice Management (for company admin)
    Route::get('admin/operative-invoices', [InvoiceController::class, 'adminOperativeInvoicesIndex'])->name('admin.operative-invoices.index');
    Route::post('admin/operative-invoices/{invoice}/mark-paid', [InvoiceController::class, 'markOperativeInvoicePaid'])->name('admin.operative-invoices.mark-paid');
    
    
    // Estimate Management
    Route::resource('estimates', EstimateController::class);
    Route::post('estimates/{estimate}/send', [EstimateController::class, 'send'])->name('estimates.send');
    Route::post('estimates/{estimate}/approve', [EstimateController::class, 'approve'])->name('estimates.approve');
    Route::post('estimates/{estimate}/reject', [EstimateController::class, 'reject'])->name('estimates.reject');
    Route::post('estimates/{estimate}/convert-to-project', [EstimateController::class, 'convertToProject'])->name('estimates.convert-to-project');
    Route::get('estimates/{estimate}/pdf', [EstimateController::class, 'pdf'])->name('estimates.pdf');
    Route::post('estimates/{estimate}/duplicate', [EstimateController::class, 'duplicate'])->name('estimates.duplicate');
    Route::post('estimates/create-from-template', [EstimateController::class, 'createFromTemplate'])->name('estimates.create-from-template');
    
    // Expense Management
    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses/{expense}/submit', [ExpenseController::class, 'submit'])->name('expenses.submit');
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
    Route::post('expenses/{expense}/mark-reimbursed', [ExpenseController::class, 'markReimbursed'])->name('expenses.mark-reimbursed');
    Route::get('expenses/{expense}/receipt', [ExpenseController::class, 'downloadReceipt'])->name('expenses.download-receipt');
    Route::get('expense-reports', [ExpenseController::class, 'reports'])->name('expenses.reports');
    Route::post('expenses/bulk-approve', [ExpenseController::class, 'bulkApprove'])->name('expenses.bulk-approve');
    
    // Financial Reports
    Route::get('financial-reports', [FinancialReportsController::class, 'index'])->name('financial-reports.index');
    Route::get('financial-reports/export', [FinancialReportsController::class, 'export'])->name('financial-reports.export');
    Route::get('financial-reports/expenses', [FinancialReportController::class, 'expenses'])->name('financial-reports.expenses');
    Route::get('financial-reports/profit-loss', [FinancialReportController::class, 'profitLoss'])->name('financial-reports.profit-loss');
    Route::get('financial-reports/cash-flow', [FinancialReportController::class, 'cashFlow'])->name('financial-reports.cash-flow');

    // Placeholder Modules
    // Project Schedule Management
    Route::resource('project-schedules', ProjectScheduleController::class);
    Route::patch('project-schedules/{projectSchedule}/progress', [ProjectScheduleController::class, 'updateProgress'])->name('project-schedules.update-progress');
    Route::patch('project-schedules/{projectSchedule}/status', [ProjectScheduleController::class, 'updateStatus'])->name('project-schedules.update-status');
    Route::post('project-schedules/reorder', [ProjectScheduleController::class, 'reorder'])->name('project-schedules.reorder');
    Route::get('project-schedules/gantt-data', [ProjectScheduleController::class, 'ganttData'])->name('project-schedules.gantt-data');
    Route::get('project-schedules/calendar-events', [ProjectScheduleController::class, 'calendarEvents'])->name('project-schedules.calendar-events');
    Route::get('project-schedules/timeline-data', [ProjectScheduleController::class, 'timelineData'])->name('project-schedules.timeline-data');
    
    // Health & Safety Management
    Route::prefix('health-safety')->name('health-safety.')->group(function () {
        Route::get('/', [HealthSafetyController::class, 'index'])->name('index');
        
        // RAMS
        Route::get('rams', [HealthSafetyController::class, 'rams'])->name('rams');
        Route::get('rams/create', [HealthSafetyController::class, 'createRams'])->name('rams.create');
        Route::post('rams', [HealthSafetyController::class, 'storeRams'])->name('rams.store');
        
        // Toolbox Talks
        Route::get('toolbox-talks', [HealthSafetyController::class, 'toolboxTalks'])->name('toolbox-talks');
        Route::get('toolbox-talks/create', [HealthSafetyController::class, 'createToolboxTalk'])->name('toolbox-talks.create');
        Route::post('toolbox-talks', [HealthSafetyController::class, 'storeToolboxTalk'])->name('toolbox-talks.store');
        
        // Incidents
        Route::get('incidents', [HealthSafetyController::class, 'incidents'])->name('incidents');
        Route::get('incidents/create', [HealthSafetyController::class, 'createIncident'])->name('incidents.create');
        Route::post('incidents', [HealthSafetyController::class, 'storeIncident'])->name('incidents.store');
        
        // Inductions
        Route::get('inductions', [HealthSafetyController::class, 'inductions'])->name('inductions');
        Route::get('inductions/create', [HealthSafetyController::class, 'createInduction'])->name('inductions.create');
        Route::post('inductions', [HealthSafetyController::class, 'storeInduction'])->name('inductions.store');
        Route::get('inductions/{induction}', [HealthSafetyController::class, 'showInduction'])->name('inductions.show');
        Route::get('inductions/{induction}/certificate', [HealthSafetyController::class, 'downloadCertificate'])->name('inductions.certificate');
        Route::patch('inductions/{induction}/renew', [HealthSafetyController::class, 'renewInduction'])->name('inductions.renew');
        Route::patch('inductions/{induction}/suspend', [HealthSafetyController::class, 'suspendInduction'])->name('inductions.suspend');
        Route::patch('inductions/{induction}/reactivate', [HealthSafetyController::class, 'reactivateInduction'])->name('inductions.reactivate');
        
        // Custom Forms
        Route::get('forms', [HealthSafetyController::class, 'forms'])->name('forms');
        Route::get('forms/template/create', [HealthSafetyController::class, 'createFormTemplate'])->name('forms.template.create');
        Route::post('forms/template', [HealthSafetyController::class, 'storeFormTemplate'])->name('forms.template.store');
        Route::get('forms/submit/{templateId}', [HealthSafetyController::class, 'submitForm'])->name('forms.submit');
        Route::post('forms/submit/{templateId}', [HealthSafetyController::class, 'storeFormSubmission'])->name('forms.submission.store');
        
        // Safety Observations
        Route::get('observations', [HealthSafetyController::class, 'observations'])->name('observations');
    });
    
    // Field Operations Routes
    Route::get('field-operations', [FieldOperationsController::class, 'index'])->name('field-operations.index');
    Route::get('field-operations/equipment', [FieldOperationsController::class, 'equipment'])->name('field-operations.equipment');
    Route::get('field-operations/materials', [FieldOperationsController::class, 'materials'])->name('field-operations.materials');
    Route::get('field-operations/safety', [FieldOperationsController::class, 'safety'])->name('field-operations.safety');
    Route::get('field-operations/work-orders', [FieldOperationsController::class, 'workOrders'])->name('field-operations.work-orders');
    
    // Time Tracking Routes
    Route::get('time-tracking', [TimeTrackingController::class, 'index'])->name('time-tracking.index');
    Route::get('time-tracking/timesheets', [TimeTrackingController::class, 'timesheets'])->name('time-tracking.timesheets');
    Route::get('time-tracking/reports', [TimeTrackingController::class, 'reports'])->name('time-tracking.reports');
    Route::post('time-tracking/clock-in-out', [TimeTrackingController::class, 'clockInOut'])->name('time-tracking.clock-in-out');
    
    // Keep old routes for backward compatibility
    Route::get('resources', [FieldOperationsController::class, 'index'])->name('resources.index');
    Route::get('time-entries', [TimeTrackingController::class, 'index'])->name('time-entries.index');
    Route::get('messages', [PlaceholderController::class, 'messaging'])->name('messages.index');
    Route::get('payments', [PlaceholderController::class, 'payments'])->name('payments.index');

    // Membership Routes
    Route::get('/membership', [App\Http\Controllers\MembershipController::class, 'index'])->name('membership.index');
    Route::put('/membership', [App\Http\Controllers\MembershipController::class, 'update'])->name('membership.update');

    // Company Settings Routes
    Route::get('/settings', [App\Http\Controllers\CompanySettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\CompanySettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/logo', [App\Http\Controllers\CompanySettingsController::class, 'uploadLogo'])->name('settings.logo.upload');
    Route::delete('/settings/logo', [App\Http\Controllers\CompanySettingsController::class, 'removeLogo'])->name('settings.logo.remove');
    
    // Email Settings Routes (integrated with settings)
    Route::post('/settings/email/test', [App\Http\Controllers\CompanySettingsController::class, 'testEmail'])->name('settings.email.test');
    Route::get('/settings/email/usage', [App\Http\Controllers\CompanySettingsController::class, 'emailUsage'])->name('settings.email.usage');
    Route::post('/settings/email/preview', [App\Http\Controllers\CompanySettingsController::class, 'previewEmail'])->name('settings.email.preview');

    // Email Settings Routes (moved to settings tab)
    // Route::get('/email-settings', [App\Http\Controllers\EmailSettingsController::class, 'index'])->name('email-settings.index');
    // Route::post('/email-settings', [App\Http\Controllers\EmailSettingsController::class, 'store'])->name('email-settings.store');
    // Route::post('/email-settings/test', [App\Http\Controllers\EmailSettingsController::class, 'test'])->name('email-settings.test');
    // Route::get('/email-settings/usage', [App\Http\Controllers\EmailSettingsController::class, 'usage'])->name('email-settings.usage');
    // Route::post('/email-settings/preview', [App\Http\Controllers\EmailSettingsController::class, 'preview'])->name('email-settings.preview');

    // Operative Data Forms (Admin)
    Route::prefix('admin/operative-data-forms')->name('admin.operative-data-forms.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OperativeDataFormController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\OperativeDataFormController::class, 'create'])->name('create');
        Route::post('/generate-link', [\App\Http\Controllers\OperativeDataFormController::class, 'generateShareLink'])->name('generate-link');
        Route::get('/{form}', [\App\Http\Controllers\OperativeDataFormController::class, 'show'])->name('show');
        Route::patch('/{form}/approve', [\App\Http\Controllers\OperativeDataFormController::class, 'approve'])->name('approve');
        Route::patch('/{form}/reject', [\App\Http\Controllers\OperativeDataFormController::class, 'reject'])->name('reject');
        Route::post('/{form}/create-account', [\App\Http\Controllers\OperativeDataFormController::class, 'createAccount'])->name('create-account');
    });

    // Profile routes
    Route::get('/profile', function () {
        return view('profile.show');
    })->name('profile.show');
});

// Public Operative Data Form Routes (no auth required)
Route::get('/operative-data-form/{token}', [\App\Http\Controllers\OperativeDataFormController::class, 'showPublicForm'])->name('operative-data-form.show');
Route::post('/operative-data-form/{token}', [\App\Http\Controllers\OperativeDataFormController::class, 'submitPublicForm'])->name('operative-data-form.submit');