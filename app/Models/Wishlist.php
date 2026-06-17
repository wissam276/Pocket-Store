<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wishlist extends Model
{
    use HasFactory;
    protected $table = 'wishlists';
    protected $fillable = [
        //'id',
        'user_id',
        'item_id',
    ];

    public function Item(){
        return $this->belongsToMany(Item::class);
    }

    public function User(){
        return $this->belongsTo(User::class);
    }
}
