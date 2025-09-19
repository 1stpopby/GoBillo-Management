<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\ProjectExpense;
use App\Observers\ProjectExpenseObserver;
use App\Models\Invoice;
use App\Observers\InvoiceObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS URLs when running on Replit
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }
        
        // Also check for Replit domain environment
        if (isset($_ENV['REPLIT_DOMAINS']) || isset($_ENV['REPLIT_DEV_DOMAIN'])) {
            URL::forceScheme('https');
        }

        ProjectExpense::observe(ProjectExpenseObserver::class);
        Invoice::observe(InvoiceObserver::class);
    }
}
