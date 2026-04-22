<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'stripe_session_id', 'first_name', 'last_name',
        'email', 'phone', 'total', 'status'
    ];

    public function items() { return $this->hasMany(OrderItem::class); }
}
