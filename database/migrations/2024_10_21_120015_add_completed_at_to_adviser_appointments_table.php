<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable(); // Add completed_at field
        });
    }
    
    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
    
};
