<?php

use App\Http\Controllers\Api\V1\AssetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Assets API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes for the Assets management module.
|
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.v1.')->group(function () {
    
    // Assets API
    Route::apiResource('assets', AssetController::class);
    
    // Lookups endpoint
    Route::get('lookups', [AssetController::class, 'lookups'])->name('lookups');
    
});




