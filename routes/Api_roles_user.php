<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Roles
Route::apiResource('roles', RoleController::class);

// Users
Route::apiResource('users', UserController::class);