<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Module;
use App\Models\Company;
use App\Models\User;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share enabled modules with all views
        View::composer('*', function ($view) {
            try {
                if (auth()->check() && auth()->user() && auth()->user()->company_id) {
                    $user = auth()->user();
                    $company = $user->company;
                    
                    if ($company) {
                        $enabledModules = $company->enabledModules()->get();
                        
                        $view->with('enabledModules', $enabledModules);
                        $view->with('hasModule', function($moduleName) use ($company) {
                            return $company->hasModule($moduleName);
                        });
                    } else {
                        $view->with('enabledModules', collect());
                        $view->with('hasModule', function($moduleName) {
                            return false;
                        });
                    }
                } else {
                    $view->with('enabledModules', collect());
                    $view->with('hasModule', function($moduleName) {
                        return false;
                    });
                }
            } catch (\Exception $e) {
                // Fallback in case of any errors
                $view->with('enabledModules', collect());
                $view->with('hasModule', function($moduleName) {
                    return false;
                });
            }
        });
    }
} 