<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;

/**
 * Week 4 - Employee CRUD API with validation.
 */
class EmployeeController extends Controller
{
    /**
     * GET /api/employees
     */
    public function index()
    {
        return EmployeeResource::collection(Employee::all());
    }

    /**
     * POST /api/employees
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:employees,email',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
        ]);

        $employee = Employee::create($validated);

        return (new EmployeeResource($employee))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/employees/{id}
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return new EmployeeResource($employee);
    }

    /**
     * PUT/PATCH /api/employees/{id}
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|max:255|unique:employees,email,'.$employee->id,
            'department' => 'sometimes|required|string|max:100',
            'position' => 'sometimes|required|string|max:100',
        ]);

        $employee->update($validated);

        return new EmployeeResource($employee);
    }

    /**
     * DELETE /api/employees/{id}
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
