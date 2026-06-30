<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('socios', SocioController::class);
Route::resource('medidores', MedidorController::class);
Route::resource('lecturas', LecturaController::class);