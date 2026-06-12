<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectListsController;
use App\Http\Controllers\WaWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks/store', [TaskController::class, 'store']);

Route::post('/wa-webhook', [WaWebhookController::class, 'handle']);

Route::middleware('auth')->group(function () {
    Route::post('/wa-izin-send-test', [WaWebhookController::class, 'sendTest']);
});