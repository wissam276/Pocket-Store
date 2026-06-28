<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    public const TYPE_PERCENTAGE = 'Percentage';
    public const TYPE_FIXED = 'Fixed';
    public const TYPE_FREE_SHIPPING = 'FreeShipping';

    protected $fillable = [
        'code',
        'type',
        'value',
        'start_date',
        'end_date',
        'usage_limit',
        'min_order',
        'enabled',
        'usage_count',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'usage_limit' => 'integer',
        'min_order' => 'decimal:2',
        'enabled' => 'boolean',
        'usage_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Coupon $coupon): void {
            $coupon->code = strtoupper(trim($coupon->code));
        });
    }

    public function scopeLive(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query
            ->where('enabled', true)
            ->where(function (Builder $query) use ($today): void {
                $query->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', $today);
            })
            ->where(function (Builder $query) use ($today): void {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->where(function (Builder $query): void {
                $query->whereNull('usage_limit')
                    ->orWhereColumn('usage_count', '<', 'usage_limit');
            });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isLive(): bool
    {
        $today = now()->startOfDay();

        if (! $this->enabled) {
            return false;
        }

        if ($this->start_date && $this->start_date->startOfDay()->gt($today)) {
            return false;
        }

        if ($this->end_date && $this->end_date->endOfDay()->lt(now())) {
            return false;
        }

        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canApplyTo(float $subtotal): bool
    {
        return $this->isLive() && $subtotal >= (float) $this->min_order;
    }

    public function discountFor(float $subtotal): float
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return round($subtotal * ((float) $this->value / 100), 2);
        }

        if ($this->type === self::TYPE_FIXED) {
            return min($subtotal, (float) $this->value);
        }

        return 0.0;
    }
}
