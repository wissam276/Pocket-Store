<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'item_id', 'rating', 'comment'];
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        static::created(function ($rating) {
            if ($rating->item) {
                $rating->item->updateAverageRating();
            }
        });

        static::deleted(function ($rating) {
            if ($rating->item) {
                $rating->item->updateAverageRating();
            }
        });
    }
}
