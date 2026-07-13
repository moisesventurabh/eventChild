<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('/user', function (Request $request) { 
            return $request->user(); 
        });
        
        Route::get('/events', [EventController::class, 'index']);
        Route::post('/events', [EventController::class, 'store']);
        Route::get('/events/{event}', [EventController::class, 'show']);
        Route::post('/events/{event}/refresh', [EventController::class, 'refresh']);
    });
});