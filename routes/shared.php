<?php

use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    $host = request()->getHost();
    return response()->file(public_path("sitemaps/{$host}.xml"));
});

Route::get('/robots.txt', function () {
    $host = request()->getHost();
    return response()->file(public_path("robots/{$host}.txt"));
});