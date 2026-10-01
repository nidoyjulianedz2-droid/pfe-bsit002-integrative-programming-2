<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $it = Department::where('name', 'IT')->first();
        $hr = Department::where('name', 'HR')->first();

        // Fixed records used in the module examples (search=juan, department=IT).
        Employee::firstOrCreate(['email' => 'juan@example.com'], [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'department_id' => $it->id,
            'position' => 'Programmer',
        ]);

        Employee::firstOrCreate(['email' => 'maria@example.com'], [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'department_id' => $hr->id,
            'position' => 'HR Officer',
        ]);

        // 23 random employees spread across the existing departments,
        // so pagination (10 per page) shows 3 pages.
        $departments = Department::pluck('id');

        Employee::factory()
            ->count(23)
            ->state(fn () => ['department_id' => $departments->random()])
            ->create();
    }
}
