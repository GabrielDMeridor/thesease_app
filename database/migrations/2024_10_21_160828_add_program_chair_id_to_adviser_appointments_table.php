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
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Add the program_chair_id column as a nullable foreign key
            $table->unsignedBigInteger('program_chair_id')->nullable()->after('adviser_id');

            // Define a foreign key constraint for program_chair_id (you can skip this if no foreign key constraint is needed)
            $table->foreign('program_chair_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Drop the program_chair_id column and its foreign key constraint
            $table->dropForeign(['program_chair_id']);
            $table->dropColumn('program_chair_id');
        });
    }
};
