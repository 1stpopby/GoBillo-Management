<?php

namespace App\Providers;

use App\Models\Client;
use App\Policies\ClientPolicy;
use App\Models\Expense;
use App\Policies\ExpensePolicy;
use App\Models\Invoice;
use App\Policies\InvoicePolicy;
use App\Models\OperativeDataForm;
use App\Policies\OperativeDataFormPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Client::class => ClientPolicy::class,
        Expense::class => ExpensePolicy::class,
        Invoice::class => InvoicePolicy::class,
        OperativeDataForm::class => OperativeDataFormPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}


