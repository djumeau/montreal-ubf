<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/resources', function () {
    return '<h1>Resources</h1>';
});
