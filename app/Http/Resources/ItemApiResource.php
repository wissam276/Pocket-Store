<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemApiResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'slug'               => $this->slug,


            'category_id'        => $this->category_id,
            'quantity'           => $this->quantity,
            'sales_count'        => $this->sales_count,
            'DiscountPercentage' => $this->DiscountPercentage,
            'short_description' => $this->short_description,


            'price'              => $this->price,
            'priceAfterDiscount' => $this->priceAfterDiscount,
            'description'        => $this->description,
            'company'            => $this->company,
            'availability'       => (bool) $this->availability,

            'item_image' => asset('storage/' . $this->item_image),

            'details_images' => array_map(
                fn($path) => asset('storage/' . $path),
                json_decode($this->details_image, true) ?? []
            ),

            'stats' => [
                'average_rating' => $this->average_rating,
                'ratings_count'  => $this->ratings()->count(),
            ],

            'reviews' => $this->ratings->map(function ($rating) {
                return [
                    'user'    => $rating->user->name ?? 'مستخدم',
                    'rating'  => $rating->rating,
                    'comment' => $rating->comment,
                    'date'    => $rating->created_at->diffForHumans(),
                ];
            }),
        ];
    }
}
