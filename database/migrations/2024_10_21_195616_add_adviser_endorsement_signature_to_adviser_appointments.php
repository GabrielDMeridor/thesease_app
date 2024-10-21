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
            $table->string('adviser_endorsement_signature')->nullable();  // New endorsement signature column
        });
    }
    
    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('adviser_endorsement_signature');
        });
    }
    
};
