<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\VideoController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/v1/get-category', [CategoryController::class, 'index']);
Route::post('/v1/create-category', [CategoryController::class, 'store']);
Route::put('/v1/update-category/{id}', [CategoryController::class, 'update']);
Route::delete('/v1/delete-category/{id}', [
    CategoryController::class,
    'destroy',
]);

Route::get('/v1/get-module', [ModuleController::class, 'index']);
Route::post('/v1/create-module', [ModuleController::class, 'store']);
Route::put('/v1/update-module/{id}', [ModuleController::class, 'update']);
Route::delete('/v1/delete-module/{id}', [ModuleController::class, 'destroy']);

Route::get('/v1/get-video', [VideoController::class, 'index']);
Route::post('/v1/upload-video', [VideoController::class, 'store']);
Route::post('/v1/update-video/{id}', [VideoController::class, 'update']);
Route::delete('/v1/delete-video/{id}', [VideoController::class, 'destroy']);

Route::post('/v1/login', [AuthController::class, 'login']);
Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/logout', [AuthController::class, 'logout']);
Route::post('/v1/refresh', [AuthController::class, 'refresh']);
