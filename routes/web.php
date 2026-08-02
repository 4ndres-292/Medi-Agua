<?php

use Illuminate\Support\Facades\Route;

Route::get('/reset-password/{token}', function ($token) {
    return view('app');
})->name('password.reset');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

// Usa esto (más elegante)
//Route::view('/{any}', 'app')->where('any', '.*');