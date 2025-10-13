<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/payment-callback', function () {
    return view('payment-callback');
});

require __DIR__.'/auth.php';
