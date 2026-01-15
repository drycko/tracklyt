<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminTenantController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminBillingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectRepositoryController;
use App\Http\Controllers\ProjectLinkController;
use App\Http\Controllers\MobileAppMetadataController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\MaintenanceProfileController;
use App\Http\Controllers\MaintenanceReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AccessControlController;
use App\Http\Controllers\OnboardingController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Auth routes (login, register, password reset, etc.)
Auth::routes();

// Client Portal Routes (no auth required for login, magic link)
Route::prefix('client')->name('client.')->group(function () {
    // client public index route
    Route::get('/', [ClientPortalController::class, 'index'])->name('index');
    // Public client auth routes
    Route::get('login', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('send-magic-link', [ClientAuthController::class, 'sendMagicLink'])->name('send-magic-link');
    Route::get('auth/{token}', [ClientAuthController::class, 'authenticate'])->name('auth');
    
    // Protected client portal routes
    Route::middleware(['client.auth'])->group(function () {
        Route::get('dashboard', [ClientPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('logout', [ClientAuthController::class, 'logout'])->name('logout');
        
        // Quotes
        Route::get('quotes', [ClientPortalController::class, 'quotes'])->name('quotes.index');
        Route::get('quotes/{quote}', [ClientPortalController::class, 'showQuote'])->name('quotes.show');
        Route::get('quotes/{quote}/pdf', [ClientPortalController::class, 'downloadQuotePdf'])->name('quotes.pdf');
        
        // Projects
        Route::get('projects', [ClientPortalController::class, 'projects'])->name('projects.index');
        Route::get('projects/{project}', [ClientPortalController::class, 'showProject'])->name('projects.show');
        
        // Invoices
        Route::get('invoices', [ClientPortalController::class, 'invoices'])->name('invoices.index');
        Route::get('invoices/{invoice}', [ClientPortalController::class, 'showInvoice'])->name('invoices.show');
        Route::get('invoices/{invoice}/pdf', [ClientPortalController::class, 'downloadInvoicePdf'])->name('invoices.pdf');
    });
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    
    // Platform Admin Routes (Super Admin Only)
    Route::middleware(['super_admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/revenue', [AdminDashboardController::class, 'revenue'])->name('revenue');
        Route::get('/usage', [AdminDashboardController::class, 'usage'])->name('usage');
        
        // Tenants Management
        Route::resource('tenants', AdminTenantController::class);
        Route::post('tenants/{tenant}/suspend', [AdminTenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('tenants/{tenant}/activate', [AdminTenantController::class, 'activate'])->name('tenants.activate');
        
        // Subscription Management
        Route::get('subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/{subscription}', [AdminSubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::get('subscriptions/{subscription}/edit', [AdminSubscriptionController::class, 'edit'])->name('subscriptions.edit');
        Route::put('subscriptions/{subscription}', [AdminSubscriptionController::class, 'update'])->name('subscriptions.update');
        Route::post('subscriptions/{subscription}/extend-trial', [AdminSubscriptionController::class, 'extendTrial'])->name('subscriptions.extend-trial');
        Route::post('subscriptions/{subscription}/suspend', [AdminSubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
        Route::post('subscriptions/{subscription}/activate', [AdminSubscriptionController::class, 'activate'])->name('subscriptions.activate');
        Route::post('subscriptions/{subscription}/cancel', [AdminSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('subscriptions/{subscription}/resume', [AdminSubscriptionController::class, 'resume'])->name('subscriptions.resume');
        Route::get('subscriptions/{subscription}/usage', [AdminSubscriptionController::class, 'usage'])->name('subscriptions.usage');
        Route::post('subscriptions/{subscription}/reset-usage', [AdminSubscriptionController::class, 'resetUsage'])->name('subscriptions.reset-usage');
        
        // Plan Management
        Route::resource('plans', AdminPlanController::class);
        Route::post('plans/{plan}/toggle-active', [AdminPlanController::class, 'toggleActive'])->name('plans.toggle-active');
        Route::post('plans/{plan}/toggle-featured', [AdminPlanController::class, 'toggleFeatured'])->name('plans.toggle-featured');
        
        // Billing & Revenue
        Route::get('billing', [AdminBillingController::class, 'index'])->name('billing.index');
        Route::get('billing/report', [AdminBillingController::class, 'report'])->name('billing.report');
        Route::get('billing/export', [AdminBillingController::class, 'export'])->name('billing.export');
    });

    // Tenant-scoped routes (require tenant context)
    Route::middleware(['tenant'])->group(function () {
        
        // Onboarding (exempt from onboarded check)
        Route::prefix('onboarding')->name('onboarding.')->group(function () {
            Route::get('/', [OnboardingController::class, 'index'])->name('index');
            Route::post('/company', [OnboardingController::class, 'saveCompanyInfo'])->name('save-company');
            Route::post('/team', [OnboardingController::class, 'saveTeamSetup'])->name('save-team');
            Route::post('/quick-start', [OnboardingController::class, 'saveQuickStart'])->name('save-quick-start');
            Route::post('/preferences', [OnboardingController::class, 'savePreferences'])->name('save-preferences');
            Route::get('/complete', [OnboardingController::class, 'complete'])->name('complete');
            Route::post('/skip', [OnboardingController::class, 'skip'])->name('skip');
        });
        
        // All other tenant routes require onboarding to be completed
        Route::middleware(['onboarded'])->group(function () {
            
            // Dashboard
            Route::get('/home', [HomeController::class, 'index'])->name('home');
            Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
        
        // Subscription Management (Tenant-side)
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('index');
            Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans');
            Route::post('/select-plan', [SubscriptionController::class, 'selectPlan'])->name('select-plan');
            Route::get('/upgrade', [SubscriptionController::class, 'showUpgrade'])->name('upgrade');
            Route::post('/upgrade', [SubscriptionController::class, 'upgrade'])->name('process-upgrade');
            Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
            Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
            Route::get('/payment-method', [SubscriptionController::class, 'paymentMethod'])->name('payment-method');
            Route::post('/payment-method', [SubscriptionController::class, 'updatePaymentMethod'])->name('update-payment-method');
            Route::get('/billing-history', [SubscriptionController::class, 'billingHistory'])->name('billing-history');
        });
        
        // Reports
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        
        // Settings (permissions checked in controller)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::put('/profile', [SettingsController::class, 'updateProfile'])->name('update-profile');
            Route::put('/company', [SettingsController::class, 'updateCompany'])->name('update-company');
            Route::put('/billing', [SettingsController::class, 'updateBilling'])->name('update-billing');
            Route::put('/preferences', [SettingsController::class, 'updatePreferences'])->name('update-preferences');
            Route::put('/team/{user}', [SettingsController::class, 'updateTeamMember'])->name('update-team-member');
            Route::delete('/team/{user}', [SettingsController::class, 'deleteTeamMember'])->name('delete-team-member');
        });
        
        // Access Control (Owner & Admin only, permissions checked in controller)
        Route::prefix('access-control')->name('access-control.')->group(function () {
            Route::get('/', [AccessControlController::class, 'index'])->name('index');
            Route::post('/roles', [AccessControlController::class, 'storeRole'])->name('store-role');
            Route::put('/roles/{role}/permissions', [AccessControlController::class, 'updateRolePermissions'])->name('update-role-permissions');
            Route::delete('/roles/{role}', [AccessControlController::class, 'destroyRole'])->name('destroy-role');
            Route::post('/permissions', [AccessControlController::class, 'storePermission'])->name('store-permission');
            Route::delete('/permissions/{permission}', [AccessControlController::class, 'destroyPermission'])->name('destroy-permission');
            Route::put('/users/{user}/roles', [AccessControlController::class, 'assignRole'])->name('assign-role');
            Route::put('/users/{user}/permissions', [AccessControlController::class, 'assignPermission'])->name('assign-permission');
        });
        
        // Clients
        Route::resource('clients', ClientController::class);

        // Quotes
        Route::resource('quotes', QuoteController::class);
        Route::patch('quotes/{quote}/status', [QuoteController::class, 'updateStatus'])->name('quotes.update-status');
        Route::post('quotes/{quote}/duplicate', [QuoteController::class, 'duplicate'])->name('quotes.duplicate');
        Route::post('quotes/{quote}/convert', [QuoteController::class, 'convert'])->name('quotes.convert');
        Route::get('quotes/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('quotes.pdf');

        // Projects
        Route::resource('projects', ProjectController::class);
        Route::post('projects/from-quote/{quote}', [ProjectController::class, 'createFromQuote'])->name('projects.from-quote');
        
        // Project Repositories
        Route::resource('projects.repositories', ProjectRepositoryController::class)->shallow();
        Route::post('repositories/{repository}/set-primary', [ProjectRepositoryController::class, 'setPrimary'])->name('repositories.set-primary');
        
        // Project Links
        Route::resource('projects.links', ProjectLinkController::class)->shallow();
        
        // Mobile App Metadata
        Route::resource('projects.mobile-apps', MobileAppMetadataController::class)->shallow();

        // Tasks
        Route::resource('tasks', TaskController::class);
        Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
        Route::post('tasks/{task}/reopen', [TaskController::class, 'reopen'])->name('tasks.reopen');

        // Time Entries
        Route::resource('time-entries', TimeEntryController::class);
        Route::post('time-entries/start', [TimeEntryController::class, 'start'])->name('time-entries.start');
        Route::post('time-entries/{timeEntry}/stop', [TimeEntryController::class, 'stop'])->name('time-entries.stop');
        Route::post('time-entries/{timeEntry}/lock', [TimeEntryController::class, 'lock'])->name('time-entries.lock');
        Route::post('time-entries/{timeEntry}/unlock', [TimeEntryController::class, 'unlock'])->name('time-entries.unlock');
        Route::get('time-entries/running/current', [TimeEntryController::class, 'getCurrentRunning'])->name('time-entries.current');

        // Maintenance Profiles
        Route::resource('maintenance-profiles', MaintenanceProfileController::class);
        Route::post('maintenance-profiles/{maintenanceProfile}/reset', [MaintenanceProfileController::class, 'reset'])->name('maintenance-profiles.reset');
        Route::get('maintenance-profiles/{maintenanceProfile}/usage', [MaintenanceProfileController::class, 'showUsage'])->name('maintenance-profiles.usage');

        // Invoices
        Route::get('invoices/unbilled-entries', [InvoiceController::class, 'unbilledEntries'])->name('invoices.unbilled-entries');
        Route::get('invoices/create/from-time-entries', [InvoiceController::class, 'createFromTimeEntries'])->name('invoices.create-from-time');
        Route::post('invoices/generate/from-time-entries', [InvoiceController::class, 'generateFromTimeEntries'])->name('invoices.generate-from-time');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
        Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::post('invoices/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])->name('invoices.mark-sent');
        Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('invoices.payments.store');
        Route::delete('invoices/{invoice}/payments/{payment}', [InvoiceController::class, 'destroyPayment'])->name('invoices.payments.destroy');
        Route::resource('invoices', InvoiceController::class);

        // Maintenance Reports
        Route::get('maintenance-reports/{maintenanceReport}/start', [MaintenanceReportController::class, 'start'])->name('maintenance-reports.start');
        Route::post('maintenance-reports/{maintenanceReport}/complete', [MaintenanceReportController::class, 'complete'])->name('maintenance-reports.complete');
        Route::get('maintenance-reports/{maintenanceReport}/pdf', [MaintenanceReportController::class, 'downloadPdf'])->name('maintenance-reports.pdf');
        Route::put('maintenance-reports/{maintenanceReport}/tasks/{task}', [MaintenanceReportController::class, 'updateTask'])->name('maintenance-reports.tasks.update');
        Route::delete('maintenance-reports/{maintenanceReport}/tasks/{task}/screenshots/{screenshotIndex}', [MaintenanceReportController::class, 'deleteScreenshot'])->name('maintenance-reports.tasks.screenshots.delete');
        Route::resource('maintenance-reports', MaintenanceReportController::class);

        // Reports (placeholder for future)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('time-tracking', function () {
                return view('reports.time-tracking');
            })->name('time-tracking');
            
            Route::get('revenue', function () {
                return view('reports.revenue');
            })->name('revenue');
            
            Route::get('projects', function () {
                return view('reports.projects');
            })->name('projects');
        });

        // User Management (Admin & Owner only)
        Route::middleware(['role:owner|admin'])->group(function () {
            Route::get('users', function () {
                return view('users.index');
            })->name('users.index');
            
            Route::get('users/create', function () {
                return view('users.create');
            })->name('users.create');
        });
        
        }); // End of onboarded middleware group
    }); // End of tenant middleware group
}); // End of auth middleware group
