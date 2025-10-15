<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('www.home');
});

require __DIR__ . '/shared.php';


