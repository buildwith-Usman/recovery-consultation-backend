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
        Schema::create('user_available_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('weekday', ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'])->nullable();
            $table->string('session_duration')->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('unavailable');
            $table->time('start_time')->default(null);
            $table->time('end_time')->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_available_times');
    }
};
