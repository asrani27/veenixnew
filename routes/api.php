<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TusUploadController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// TUS Routes
Route::any('/upload/{any?}', [TusUploadController::class, 'handle'])
    ->where('any', '.*');
