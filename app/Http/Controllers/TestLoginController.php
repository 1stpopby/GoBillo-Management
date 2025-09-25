<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestLoginController extends Controller
{
    public function loginAsAlex()
    {
        // Log in as Alex (company_admin with company_id = 1)
        Auth::loginUsingId(2);
        return redirect('/financial-reports');
    }
    
    public function loginAsSuperAdmin()
    {
        // Log in as SuperAdmin (with company_id = null)
        Auth::loginUsingId(1);
        return redirect('/financial-reports');
    }
}