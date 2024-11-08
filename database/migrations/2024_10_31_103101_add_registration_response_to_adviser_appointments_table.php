<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('registration_response')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('registration_response');
        });
    }
    
};
