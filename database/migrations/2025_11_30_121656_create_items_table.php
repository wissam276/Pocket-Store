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
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->integer('quantity');
            $table->string('company');
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
