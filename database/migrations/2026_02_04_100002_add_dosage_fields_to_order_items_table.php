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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_dosage_id')->nullable()->after('product_id');
            $table->string('dosage_name')->nullable()->after('product_name');

            $table->foreign('product_dosage_id')->references('id')->on('product_dosages')->onDelete('set null');
            $table->index('product_dosage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_dosage_id']);
            $table->dropIndex(['product_dosage_id']);
            $table->dropColumn(['product_dosage_id', 'dosage_name']);
        });
    }
};
