<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EventController;


Route::prefix('v1')->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events/{event}', [EventController::class, 'show']); // <- Adicione esta linha
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
