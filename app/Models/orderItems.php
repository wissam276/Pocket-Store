<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class orderItems extends Model
{
    use HasFactory;
    protected $table = 'order_items';
    protected $fillable = [
        //'id',
        'order_id',
        'item_id',
        'quantity',
        'price',
        'total_price',
    ];

    public function Item(){
        return $this->belongsTo(Item::class);
    }

    public function Order(){
        return $this->belongsTo(Order::class);
    }
}
