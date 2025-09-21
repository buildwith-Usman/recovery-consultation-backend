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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pat_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doc_user_id')->constrained('users')->onDelete('cascade');
            $table->date('date')->default(null);
            $table->time('start_time')->default(null);
            $table->time('end_time')->default(null);
            $table->integer('start_time_in_secconds')->default(0);
            $table->integer('end_time_in_secconds')->default(0);
            $table->decimal('price')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
