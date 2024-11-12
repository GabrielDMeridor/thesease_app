<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalSignaturesToAdviserAppointments extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->boolean('final_program_signature')->default(false); // Adjust 'some_column' to the last column name in the table
            $table->boolean('final_ccfp_signature')->default(false)->after('final_program_signature');
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('final_program_signature');
            $table->dropColumn('final_ccfp_signature');
        });
    }
}

