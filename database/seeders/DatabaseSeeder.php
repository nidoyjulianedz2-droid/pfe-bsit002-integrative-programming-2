<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            // password: "password" (from UserFactory)
        ]);

        $this->call([
            DepartmentSeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
