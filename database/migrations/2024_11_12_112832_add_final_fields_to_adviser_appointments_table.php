<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalFieldsToAdviserAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('final_ovpri_approval')->nullable()->after('ovpri_approval');
            $table->string('final_registration_response')->nullable()->after('registration_response');
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn(['final_ovpri_approval', 'final_registration_response']);
        });
    }
}

