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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id')->unique();
            $table->unsignedBigInteger('prescribed_by_doctor_id');
            $table->unsignedBigInteger('patient_user_id');
            $table->unsignedBigInteger('prescription_image_id')->nullable();
            $table->date('prescription_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'issued', 'dispensed'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('prescribed_by_doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('prescription_image_id')->references('id')->on('files')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('appointment_id');
            $table->index('patient_user_id');
            $table->index('prescribed_by_doctor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
