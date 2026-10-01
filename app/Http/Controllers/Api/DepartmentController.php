<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;

/**
 * Week 5 - Department resource (Department has many Employees).
 */
class DepartmentController extends Controller
{
    /**
     * GET /api/departments
     */
    public function index()
    {
        return DepartmentResource::collection(
            Department::withCount('employees')->orderBy('name')->get()
        );
    }

    /**
     * POST /api/departments
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name',
        ]);

        $department = Department::create($validated);

        return (new DepartmentResource($department))->response()->setStatusCode(201);
    }

    /**
     * GET /api/departments/{id} - includes the department's employees.
     */
    public function show($id)
    {
        $department = Department::with('employees')->withCount('employees')->find($id);

        if (! $department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return new DepartmentResource($department);
    }

    /**
     * PUT/PATCH /api/departments/{id}
     */
    public function update(Request $request, $id)
    {
        $department = Department::find($id);

        if (! $department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name,'.$department->id,
        ]);

        $department->update($validated);

        return new DepartmentResource($department);
    }

    /**
     * DELETE /api/departments/{id}
     * Note: employees of this department are also deleted (onDelete cascade).
     */
    public function destroy($id)
    {
        $department = Department::find($id);

        if (! $department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        $department->delete();

        return response()->json(['message' => 'Department deleted successfully']);
    }
}
