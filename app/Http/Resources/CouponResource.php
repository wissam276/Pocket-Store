<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'value' => (float) $this->value,
            'startDate' => $this->start_date?->toDateString(),
            'endDate' => $this->end_date?->toDateString(),
            'usageLimit' => $this->usage_limit,
            'minOrder' => (float) $this->min_order,
            'enabled' => (bool) $this->enabled,
            'usageCount' => (int) $this->usage_count,
            'isLive' => $this->isLive(),
        ];
    }
}
