<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\MedidorController;
use App\Http\Controllers\LecturaController;
use App\Http\Controllers\TarifaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\NotificacionController;

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas protegidas
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/logout', [AuthController::class, 'logout']);

    // Solo admin puede gestionar usuarios y roles
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RolController::class);
    });

    // Admin y operador pueden gestionar socios, medidores, lecturas, tarifas
    Route::middleware('role:admin,operator')->group(function () {
        Route::apiResource('socios', SocioController::class);
        Route::apiResource('medidores', MedidorController::class);
        Route::apiResource('lecturas', LecturaController::class);
        Route::apiResource('tarifas', TarifaController::class);
    });

    // Admin y cajero pueden gestionar facturas, pagos
    Route::middleware('role:admin,cashier')->group(function () {
        Route::apiResource('facturas', FacturaController::class);
        Route::apiResource('pagos', PagoController::class);
    });

    // Todos los autenticados pueden ver notificaciones
    Route::apiResource('notificaciones', NotificacionController::class);

    // Reportes: todos pueden ver
    Route::get('reportes/ingresos', [ReportesController::class, 'ingresos']);
    Route::get('reportes/deudores', [ReportesController::class, 'deudores']);
    Route::get('reportes/consumo', [ReportesController::class, 'consumo']);
});