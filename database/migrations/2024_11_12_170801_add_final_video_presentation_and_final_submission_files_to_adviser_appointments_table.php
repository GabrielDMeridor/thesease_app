<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalVideoPresentationAndFinalSubmissionFilesToAdviserAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('final_video_presentation')->nullable(); // To store video path
            $table->string('original_final_video_presentation')->nullable(); // Original filename
            $table->boolean('final_submission_files')->default(0); // Marks completion of final submission
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn(['final_video_presentation', 'original_final_video_presentation', 'final_submission_files']);
        });
    }
}
