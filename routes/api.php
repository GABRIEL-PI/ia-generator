<?php

use App\Http\Controllers\ProjectController;
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

// Rota para gerar títulos
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/generate-titles', [ProjectController::class, 'generateTitles']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::post('/test-generate-post/{project}', [ProjectController::class, 'generatePost']);

// Rota para gerar artigos
Route::middleware('auth:sanctum')->post('/generate-articles', [ProjectController::class, 'generateArticles']);

