<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\HolidayPlanController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Login and logout
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

// Hollidays
Route::middleware('auth:sanctum')->get('/holiday_plans/user/{userId}', [HolidayPlanController::class, 'index']);

Route::middleware('auth:sanctum')->post('/holiday_plans/user/{userId}', [HolidayPlanController::class, 'store']);

Route::middleware('auth:sanctum')->delete('/holiday_plans/{holiday_id}', [HolidayPlanController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/holiday/{holiday_id}', [HolidayPlanController::class, 'show']);

Route::middleware('auth:sanctum')->put('/holiday/{holiday_id}', [HolidayPlanController::class, 'update']);

//Users
Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'List_User']);