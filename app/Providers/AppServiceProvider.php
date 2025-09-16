<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        ProjectExpense::observe(ProjectExpenseObserver::class);
        Invoice::observe(InvoiceObserver::class);
    }
}
