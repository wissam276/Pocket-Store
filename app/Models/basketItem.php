<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class basketItem extends Model
{
    protected $fillable = ['cart_id', 'Item_id', 'quantity'];
    public function basket(){
        return $this->belongsTo(Basket::class);
    }

    public function Item(){
        return $this->belongsTo(Item::class);
    }
}
