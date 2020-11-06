<?php

use AtlassianConnectLaravel\Http\Controllers\PluginController;
use Illuminate\Support\Facades\Route;

Route::get('/connect', [PluginController::class, 'connect']);
Route::post('/installed', [PluginController::class, 'installed'])->name('installed');

Route::middleware('auth:plugin')->group(function () {
    Route::post('/enabled', [PluginController::class, 'enabled'])->name('enabled');
    Route::post('/disabled', [PluginController::class, 'disabled'])->name('disabled');
    Route::post('/uninstalled', [PluginController::class, 'uninstalled'])->name('uninstalled');
    Route::post('/webhook/{event}', [PluginController::class, 'webhook'])->name('webhook');
});
