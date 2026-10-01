<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Week 5 - One-to-Many: a Department has many Employees.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
