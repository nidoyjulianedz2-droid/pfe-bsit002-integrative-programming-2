<?php

use Illuminate\Support\Facades\Route;

// Week 1 - Laravel welcome page (http://127.0.0.1:8000)
Route::get('/', function () {
    return view('welcome');
});
