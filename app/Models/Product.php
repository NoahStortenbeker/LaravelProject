<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'taxonomy_group',
        'taxonomy_category',
        'taxonomy_subcategory',
        'name',
        'sku',
        'price',
        'description',
        'images',
        'is_online',
        'discount_percent',
        'created_by',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'is_online' => 'boolean',
        'discount_percent' => 'integer',
    ];

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function getImageAttribute()
    {
        $images = $this->images;
        if (is_array($images) && count($images) > 0) {
            return $images[0];
        }

        return null;
    }
}
