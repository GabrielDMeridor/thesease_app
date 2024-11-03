<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdviserAppointmentsTableForProposalProcess extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->json('proposal_manuscript_updates')->nullable()->after('proposal_manuscript');
            $table->json('panel_comments')->nullable()->after('proposal_manuscript_updates');
            $table->json('student_replies')->nullable()->after('panel_comments');
            $table->json('panel_remarks')->nullable()->after('student_replies');
            $table->json('panel_signatures')->nullable()->after('panel_remarks');
            $table->string('dean_monitoring_signature')->nullable()->after('panel_signatures');
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn([
                'proposal_manuscript_updates',
                'panel_comments',
                'student_replies',
                'panel_remarks',
                'panel_signatures',
                'dean_monitoring_signature'
            ]);
        });
    }
}

