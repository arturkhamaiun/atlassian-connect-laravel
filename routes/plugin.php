<?php

use AtlassianConnectLaravel\Http\Controllers\LifecycleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(config('descriptor')));
Route::post('/installed', [LifecycleController::class, 'installed'])->name('installed');

Route::middleware('auth:plugin')->group(function () {
    Route::post('/enabled', [LifecycleController::class, 'enabled'])->name('enabled');
    Route::post('/disabled', [LifecycleController::class, 'disabled'])->name('disabled');
    Route::post('/uninstalled', [LifecycleController::class, 'uninstalled'])->name('uninstalled');
});
