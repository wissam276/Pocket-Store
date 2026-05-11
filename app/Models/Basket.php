<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    /** @use HasFactory<\Database\Factories\BasketFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'item_id', 'quantity'];
    protected $primaryKey = 'id';
    public function user(){
        return $this->belongsTo(User::class);}
    public function item(){
        return $this->belongsTo(Item::class);}
}
