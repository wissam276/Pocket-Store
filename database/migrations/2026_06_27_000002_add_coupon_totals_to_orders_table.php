<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal_price', 10, 2)->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code')->nullable();
            $table->string('coupon_type')->nullable();
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->string('delivery_method')->nullable();
            $table->decimal('delivery_cost', 10, 2)->default(0);
            $table->string('city')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn([
                'subtotal_price',
                'coupon_id',
                'coupon_code',
                'coupon_type',
                'coupon_discount',
                'delivery_method',
                'delivery_cost',
                'city',
                'notes',
            ]);
        });
    }
};
