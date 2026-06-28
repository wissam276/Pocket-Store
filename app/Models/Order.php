<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'status',
        'subtotal_price',
        'total_price',
        'shipping_address',
        'payment_method',
        'coupon_id',
        'coupon_code',
        'coupon_type',
        'coupon_discount',
        'delivery_method',
        'delivery_cost',
        'city',
        'notes',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function items(){
        return $this->hasMany(Item::class);
    }

    public function orderItems(){
        return $this->hasMany(orderItems::class);
    }

    public function coupon(){
        return $this->belongsTo(Coupon::class);
    }
}
