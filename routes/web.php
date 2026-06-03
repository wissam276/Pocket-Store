<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/intl-test', function () {
    return [
        'php_version' => PHP_VERSION,
        'intl_loaded' => extension_loaded('intl'),
        'number_formatter' => class_exists(NumberFormatter::class),
    ];
});
