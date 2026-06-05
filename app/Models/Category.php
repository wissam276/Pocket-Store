<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{use HasFactory;

    public const DEFAULT_CATEGORIES = [
        'Electronics '
        , 'Computer Accessories'
        , 'Mobile Accessories'
        ,'Home Appliances'
        ,'Accessories'
        ,'watches'
        ,'clothes'
        ,'shoes'
        ,'books'
    ];

    protected $fillable = ['name', 'slug', 'description', 'image', 'is_active'];
    protected $table='categories';
    protected $primaryKey = 'id';
    public function items(){
        return $this->hasMany(Item::class);
    }
}
