<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_proposal_submission_completed_to_adviser_appointments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProposalSubmissionCompletedToAdviserAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->boolean('proposal_submission_completed')->default(false)->after('proposal_video_presentation');
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('proposal_submission_completed');
        });
    }
}

