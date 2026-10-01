<?php

use Illuminate\Support\Facades\Route;

/*
| This project is an API only (no web pages). Everything is tested in Postman.
| GET http://127.0.0.1:8000  -> JSON status message (Week 1 Postman test)
*/
Route::get('/', function () {
    return response()->json([
        'app' => 'Integrative Programming 2 - Employee Management API',
        'status' => 'running',
        'laravel' => app()->version(),
        'api_base_url' => url('/api'),
    ]);
});
