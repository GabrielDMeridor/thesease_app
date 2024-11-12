<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalConsultationDatesToAdviserAppointments extends Migration

{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->json('final_consultation_dates')->nullable();
            $table->string('final_adviser_endorsement_signature')->nullable();
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn(['final_consultation_dates', 'final_adviser_endorsement_signature']);
        });
    }
}

