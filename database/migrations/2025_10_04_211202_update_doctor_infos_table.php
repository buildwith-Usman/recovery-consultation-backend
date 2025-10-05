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
        Schema::table('doctor_infos', function (Blueprint $table) {
            $table->enum('commision_type', ['flat', 'percentage'])->default('flat')->after('age');
            $table->string('commision_value')->nullable()->default(null)->after('commision_type');
            $table->enum('status', ['pending', 'approved', 'rejected'])->after('completed');
            $table->dropColumn('approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_infos', function (Blueprint $table) {
            $table->dropColumn('commision_type');
            $table->dropColumn('commision_value');
            $table->dropColumn('status');
            $table->boolean('approved')->default(false)->after('age');
        });
    }
};
