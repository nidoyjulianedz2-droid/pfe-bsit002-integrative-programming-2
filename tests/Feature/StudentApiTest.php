<?php

namespace Tests\Feature;

use Tests\TestCase;

class StudentApiTest extends TestCase
{
    public function test_week2_students_endpoint_returns_json(): void
    {
        $this->getJson('/api/students')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Juan Dela Cruz', 'course' => 'BSIT']);
    }
}
