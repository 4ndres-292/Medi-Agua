<?php

use Illuminate\Support\Facades\Route;
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

Route::apiResource('users', UserController::class);
Route::apiResource('roles', RolController::class);
Route::apiResource('socios', SocioController::class);
Route::apiResource('medidores', MedidorController::class);
Route::apiResource('lecturas', LecturaController::class);
Route::apiResource('tarifas', TarifaController::class);
Route::apiResource('facturas', FacturaController::class);
Route::apiResource('pagos', PagoController::class);
Route::apiResource('notificaciones', NotificacionController::class);

Route::get('reportes/ingresos', [ReportesController::class, 'ingresos']);
Route::get('reportes/deudores', [ReportesController::class, 'deudores']);
Route::get('reportes/consumo', [ReportesController::class, 'consumo']);
