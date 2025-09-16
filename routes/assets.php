<?php

use App\Http\Controllers\Assets\AssetController;
use App\Http\Controllers\Assets\AttachmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Assets Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the Assets management module.
|
*/

Route::middleware(['auth', 'verified'])->prefix('assets')->name('assets.')->group(function () {
    
    // Asset CRUD routes
    Route::resource('', AssetController::class)->parameters(['' => 'asset']);
    
    // Additional asset routes
    Route::post('bulk-delete', [AssetController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('bulk-restore', [AssetController::class, 'bulkRestore'])->name('bulk-restore');
    Route::post('{id}/restore', [AssetController::class, 'restore'])->name('restore');
    
    // Assignment routes
    Route::post('assign', [AssetController::class, 'assign'])->name('assign');
    Route::post('{asset}/unassign', [AssetController::class, 'unassign'])->name('unassign');
    
    // QR Code routes
    Route::get('{asset}/qr', [AssetController::class, 'showQr'])->name('qr');
    Route::get('{asset}/download-qr', [AssetController::class, 'downloadQr'])->name('download-qr');
    
    // Import/Export routes
    Route::get('export', [AssetController::class, 'export'])->name('export');
    Route::get('import', [AssetController::class, 'importForm'])->name('import.form');
    Route::post('import', [AssetController::class, 'import'])->name('import');
    Route::get('template', [AssetController::class, 'template'])->name('template');
    
    // Attachment routes
    Route::post('{asset}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('{asset}/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    
});


