<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasketItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray( $request)
    {
        return [
            'id'=>$this->id,
            'quantity'=>$this->quantity,
            'item'=>[
                'id' => $this->item->id,
                'name'=>$this->item->name,
                'price'=>$this->item->price,
                'image'=>asset('storage/' .$this->item->image),
            ]
        ];
    }
}
