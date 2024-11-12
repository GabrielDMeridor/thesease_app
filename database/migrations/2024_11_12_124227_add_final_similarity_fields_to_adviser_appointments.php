<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalSimilarityFieldsToAdviserAppointments extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('final_similarity_manuscript')->nullable(); // File path for final similarity manuscript
            $table->string('final_similarity_certificate')->nullable(); // File path for final similarity certificate
            $table->string('final_similarity_manuscript_original_name')->nullable(); // Original name of the manuscript
            $table->string('final_similarity_certificate_original_name')->nullable(); // Original name of the certificate
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn([
                'final_similarity_manuscript',
                'final_similarity_certificate',
                'final_similarity_manuscript_original_name',
                'final_similarity_certificate_original_name',
            ]);
        });
    }
}
