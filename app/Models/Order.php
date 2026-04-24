<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'currency',
        'subtotal',
        'total',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address_line',
        'shipping_zip_code',
        'shipping_city',
        'shipping_country',
        'notes',
        'paid_at',
        'payment_reference',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
