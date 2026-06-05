<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryApiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'slug'  => $this->slug,
            // إذا كانت لديك صورة للتصنيف، أضفها هنا
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,

            // يمكنك إضافة عدد المنتجات داخل كل تصنيف (اختياري)
            'items_count' => $this->whenLoaded('items', function () {
                return $this->items->count();
            }),
        ];
    }
}
