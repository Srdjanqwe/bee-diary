<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function() {

    Route::get('posts/pdf',          [\App\Http\Controllers\Api\PostController::class, 'postsPdf']);
    Route::get('posts/hive-history', [\App\Http\Controllers\Api\PostController::class, 'hiveHistoryPdf']);

    Route::apiResource('posts', \App\Http\Controllers\Api\PostController::class);
    Route::get('histories', [\App\Http\Controllers\Api\PostHistoryController::class, 'index']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::get('categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);

Route::get('abilities', function(Request $request) {
    return $request->user()->roles()->with('permissions')
        ->get()
        ->pluck('permissions')
        ->flatten()
        ->pluck('name')
        ->unique()
        ->values()
        ->toArray();
});
