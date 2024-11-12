<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalSubmissionResponseToAdviserAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->boolean('final_submission_files_response')->default(false); // Track if student responded
            $table->string('final_submission_approval_formfee')->nullable(); // Track approval by admin
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('final_submission_files_response');
            $table->dropColumn('final_submission_approval_formfee');
        });
    }
}
