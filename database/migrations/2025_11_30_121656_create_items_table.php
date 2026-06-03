<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // جعل الحقل مجرد رقم عادي يقبل القيم الفارغة (Nullable) وغير مرتبط بقيد
           // $table->unsignedBigInteger('category_id')->nullable();
           $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->integer('sales_count')->default(0);
            $table->string('company')->nullable();
            $table->enum('accepted', ['accepted', 'rejected', 'pending'])->default('pending');
            $table->decimal('priceAfterDiscount', 8, 2)->nullable();
            $table->decimal('DiscountPercentage', 5, 2)->nullable();
            $table->boolean('availability')->default(true);
            // Media
            $table->string( 'item_image');
            $table->json( 'details_image');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
