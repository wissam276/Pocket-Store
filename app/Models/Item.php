<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;
    protected $casts = [
        'details_image' => 'array',
    ];
    protected $fillable = ['name', 'description','slug','category', 'price', 'quantity'
        ,'priceAfterDiscount','DiscountPercentage','availability'
       ,'category_id','accepted','item_image','details_image','company'
    ];
    protected $primaryKey = 'id';
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function baskets(){
        return $this->hasMany(Basket::class);
    }
    public function wishlists(){
        return $this->belongsToMany(Wishlist::class);
    }
    public function ratings(){
        return $this->hasMany(Rating::class);
    }
   public function updateAverageRating()
{
    $average = $this->ratings()->avg('rating');
    $this->rating = $average ? round($average, 2) : 0;
    $this->save();
}
}
