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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('medicine_name');
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->enum('availability_status', ['in_stock', 'out_of_stock', 'low_stock'])->default('in_stock');
            $table->text('ingredients')->nullable();
            $table->enum('discount_type', ['percentage', 'flat'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->text('how_to_use')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_temporarily_hidden')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('image_id')->references('id')->on('files')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('category_id');
            $table->index('availability_status');
            $table->index('is_visible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
