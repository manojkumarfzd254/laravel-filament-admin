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
        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('mrp', 10, 2);  // Market price (before discount)  // change this to decimal type for better precision and scale.  // 10 digits for integer and 2 digits for decimal.  // This is for simplicity, in a real-world application, you should use a decimal type for monetary calculations.  // The "nullable" attribute is used to allow NULL values for the "discount" column.  // The
            $table->decimal('amount', 10, 2);
            $table->integer('quantity');
            $table->decimal('discount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
