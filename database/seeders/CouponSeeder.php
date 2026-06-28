<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => Coupon::TYPE_PERCENTAGE,
                'value' => 10,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'usage_limit' => 300,
                'min_order' => 50,
                'enabled' => true,
            ],
            [
                'code' => 'SALE20',
                'type' => Coupon::TYPE_PERCENTAGE,
                'value' => 20,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'usage_limit' => 150,
                'min_order' => 120,
                'enabled' => true,
            ],
            [
                'code' => 'FREESHIP',
                'type' => Coupon::TYPE_FREE_SHIPPING,
                'value' => 0,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'usage_limit' => 500,
                'min_order' => 75,
                'enabled' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(
                ['code' => $coupon['code']],
                array_merge($coupon, ['usage_count' => 0]),
            );
        }
    }
}
