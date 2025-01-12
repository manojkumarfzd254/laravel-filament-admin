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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('description')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('invoice_path')->nullable();  // Path to the generated invoice PDF file.  // This is stored as a string for simplicity, in a real-world application, you should store the file as a file in a storage directory.  // The "nullable" attribute is used to allow NULL values for the "invoice_path" column.  // The "enum" type is used to restrict the status values to "pending", "canceled", and "success
            $table->enum('status', ['pending', 'canceled', 'success'])->default('success');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
