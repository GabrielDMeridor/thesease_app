<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEthicsReviewFieldsToAdviserAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('ethics_proof_of_payment')->nullable();
            $table->string('ethics_proof_of_payment_filename')->nullable();
            $table->string('ethics_curriculum_vitae')->nullable();
            $table->string('ethics_curriculum_vitae_filename')->nullable();
            $table->string('ethics_research_services_form')->nullable();
            $table->string('ethics_research_services_form_filename')->nullable();
            $table->string('ethics_application_form')->nullable();
            $table->string('ethics_application_form_filename')->nullable();
            $table->string('ethics_study_protocol_form')->nullable();
            $table->string('ethics_study_protocol_form_filename')->nullable();
            $table->string('ethics_informed_consent_form')->nullable();
            $table->string('ethics_informed_consent_form_filename')->nullable();
            $table->boolean('ethics_send_data_to_aufc')->default(false);
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn([
                'ethics_proof_of_payment',
                'ethics_proof_of_payment_filename',
                'ethics_curriculum_vitae',
                'ethics_curriculum_vitae_filename',
                'ethics_research_services_form',
                'ethics_research_services_form_filename',
                'ethics_application_form',
                'ethics_application_form_filename',
                'ethics_study_protocol_form',
                'ethics_study_protocol_form_filename',
                'ethics_informed_consent_form',
                'ethics_informed_consent_form_filename',
                'ethics_send_data_to_aufc',
            ]);
        });
    }
}

