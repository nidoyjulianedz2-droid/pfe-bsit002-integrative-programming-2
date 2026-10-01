<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;

/**
 * Weeks 4 & 5 - Employee CRUD API with validation, relationships,
 * pagination, filtering, and searching.
 */
class EmployeeController extends Controller
{
    /**
     * GET /api/employees
     *
     * Optional query parameters:
     *   ?page=2              pagination (10 per page by default)
     *   ?per_page=5          change page size (1-100)
     *   ?search=juan         search first name, last name, or email
     *   ?department_id=1     filter by department id
     *   ?department=IT       filter by department name
     */
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'department_id' => 'sometimes|integer',
            'search' => 'sometimes|nullable|string|max:100',
            'department' => 'sometimes|nullable|string|max:100',
        ]);

        $query = Employee::with('department');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('department')) {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('name', $request->department);
            });
        }

        $employees = $query->orderBy('id')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return EmployeeResource::collection($employees);
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
            'department_id' => 'required|integer|exists:departments,id',
            'position' => 'required|string|max:100',
        ]);

        $employee = Employee::create($validated);

        return (new EmployeeResource($employee->load('department')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/employees/{id}
     */
    public function show($id)
    {
        $employee = Employee::with('department')->find($id);

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
            'department_id' => 'sometimes|required|integer|exists:departments,id',
            'position' => 'sometimes|required|string|max:100',
        ]);

        $employee->update($validated);

        return new EmployeeResource($employee->load('department'));
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
