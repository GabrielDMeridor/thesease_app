<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSampleInformedConsentToAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Add field for Sample Informed Consent form
            $table->string('ethics_sample_informed_consent')->nullable();
            $table->string('ethics_sample_informed_consent_filename')->nullable();
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Drop the added fields if we roll back the migration
            $table->dropColumn('ethics_sample_informed_consent');
            $table->dropColumn('ethics_sample_informed_consent_filename');
        });
    }
}
