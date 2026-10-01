<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase;

    protected Department $it;

    protected Department $hr;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());

        $this->it = Department::create(['name' => 'IT']);
        $this->hr = Department::create(['name' => 'HR']);
    }

    public function test_can_create_employee(): void
    {
        $this->postJson('/api/employees', [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@example.com',
            'department_id' => $this->it->id,
            'position' => 'Programmer',
        ])
            ->assertCreated()
            ->assertJsonPath('data.email', 'juan@example.com')
            ->assertJsonPath('data.department.name', 'IT');

        $this->assertDatabaseHas('employees', ['email' => 'juan@example.com']);
    }

    public function test_create_employee_validation(): void
    {
        Employee::factory()->create(['email' => 'juan@example.com', 'department_id' => $this->it->id]);

        $this->postJson('/api/employees', [
            'email' => 'juan@example.com',
            'department_id' => 999,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'department_id', 'position']);
    }

    public function test_can_show_update_and_delete_employee(): void
    {
        $employee = Employee::factory()->create(['department_id' => $this->it->id]);

        $this->getJson("/api/employees/{$employee->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $employee->id);

        $this->putJson("/api/employees/{$employee->id}", ['position' => 'Senior Programmer'])
            ->assertOk()
            ->assertJsonPath('data.position', 'Senior Programmer');

        $this->deleteJson("/api/employees/{$employee->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Employee deleted successfully');

        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_missing_employee_returns_404(): void
    {
        $this->getJson('/api/employees/999')->assertNotFound()
            ->assertJsonPath('message', 'Employee not found');
        $this->putJson('/api/employees/999', ['position' => 'X'])->assertNotFound();
        $this->deleteJson('/api/employees/999')->assertNotFound();
    }

    public function test_index_is_paginated(): void
    {
        Employee::factory()->count(25)->create(['department_id' => $this->it->id]);

        $this->getJson('/api/employees')
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 25)
            ->assertJsonPath('meta.last_page', 3);

        $this->getJson('/api/employees?page=3')->assertJsonCount(5, 'data');
    }

    public function test_index_search_and_filters(): void
    {
        Employee::factory()->create(['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'department_id' => $this->it->id]);
        Employee::factory()->create(['first_name' => 'Maria', 'last_name' => 'Santos', 'department_id' => $this->hr->id]);
        Employee::factory()->create(['first_name' => 'Pedro', 'last_name' => 'Reyes', 'department_id' => $this->it->id]);

        $this->getJson('/api/employees?search=juan')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.first_name', 'Juan');

        $this->getJson("/api/employees?department_id={$this->hr->id}")
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.first_name', 'Maria');

        $this->getJson('/api/employees?department=IT')
            ->assertJsonCount(2, 'data');
    }

    public function test_versioned_route_works(): void
    {
        $this->getJson('/api/v1/employees')->assertOk();
    }

    public function test_department_shows_its_employees(): void
    {
        Employee::factory()->count(2)->create(['department_id' => $this->it->id]);

        $this->getJson("/api/departments/{$this->it->id}")
            ->assertOk()
            ->assertJsonPath('data.employees_count', 2)
            ->assertJsonCount(2, 'data.employees');
    }
}
