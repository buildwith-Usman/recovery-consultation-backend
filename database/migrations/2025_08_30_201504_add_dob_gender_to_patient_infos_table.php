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
        Schema::table('patient_infos', function (Blueprint $table) {
            $table->string('dob')->nullable()->after('looking_for');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('dob');
            $table->string('blood_group')->nullable()->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_infos', function (Blueprint $table) {
            $table->dropColumn('dob');
            $table->dropColumn('gender');
        });
    }
};
