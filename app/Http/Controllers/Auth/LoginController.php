<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the post-login redirect path based on user role.
     *
     * @return string
     */
    public function redirectTo()
    {
        $user = auth()->user();
        
        if (!$user) {
            return '/home';
        }

        // Role-based dashboard redirection
        switch ($user->role) {
            case 'operative':
                return '/operative-dashboard';
            case 'client':
                // Future: could redirect to client portal
                return '/dashboard';
            case 'subcontractor':
                // Future: could redirect to subcontractor portal
                return '/dashboard';
            default:
                // Admin, project managers, contractors go to main dashboard
                return '/dashboard';
        }
    }
}
