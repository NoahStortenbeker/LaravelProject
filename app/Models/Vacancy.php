<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'hours',
        'location',
        'employment_type',
        'description',
        'images',
        'created_by',
    ];

    protected $casts = [
        'images' => 'array',
    ];
}
