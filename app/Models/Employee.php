<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'department_id',
        'position',
    ];

    /**
     * Week 5 - Inverse of One-to-Many: an Employee belongs to a Department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
