<?php

use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes  (all URLs here start with /api)
|--------------------------------------------------------------------------
*/

// ---------------------------------------------------------------
// Week 2 - Simple REST API (static JSON, public)
// GET /api/students
// ---------------------------------------------------------------
Route::get('/students', function () {
    return response()->json([
        [
            'id' => 1,
            'name' => 'Juan Dela Cruz',
            'course' => 'BSIT',
        ],
        [
            'id' => 2,
            'name' => 'Maria Santos',
            'course' => 'BSIT',
        ],
    ]);
});

// ---------------------------------------------------------------
// Weeks 4-5 - Employee Management API
// The same endpoints are available at /api/... and at /api/v1/...
// (Week 3 - API versioning).
// ---------------------------------------------------------------
$employeeApi = function () {
    // Week 4 - Employee CRUD
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);
    Route::put('/employees/{id}', [EmployeeController::class, 'update']);
    Route::patch('/employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

    // Week 5 - Departments (Department has many Employees)
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::get('/departments/{id}', [DepartmentController::class, 'show']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::patch('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);
};

Route::group([], $employeeApi);               // /api/employees
Route::prefix('v1')->group($employeeApi);     // /api/v1/employees
