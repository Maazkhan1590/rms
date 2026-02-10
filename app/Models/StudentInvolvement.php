<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentInvolvement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category',
        'count',
        'notes',
        'date',
        'academic_year',
    ];

    protected $casts = [
        'date' => 'date',
        'count' => 'integer',
    ];

    /**
     * Get total count by category.
     */
    public static function getTotalByCategory(string $category): int
    {
        return self::where('category', $category)->sum('count');
    }
}
