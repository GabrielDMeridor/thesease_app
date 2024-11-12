<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdviserAppointmentsTableForFinalProcess extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->date('final_defense_date')->nullable()->after('proposal_defense_date');
            $table->time('final_defense_time')->nullable()->after('final_defense_date');
            $table->json('final_manuscript_updates')->nullable()->after('final_defense_time');
            $table->json('final_panel_comments')->nullable()->after('final_manuscript_updates');
            $table->json('final_student_replies')->nullable()->after('final_panel_comments');
            $table->json('final_panel_remarks')->nullable()->after('final_student_replies');
            $table->json('final_panel_signatures')->nullable()->after('final_panel_remarks');
            
            // Use TEXT for large unindexed fields
            $table->text('dean_final_monitoring_signature')->nullable()->after('final_panel_signatures');
        });
    }
    

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn([
                'final_defense_date',
                'final_defense_time',
                'final_manuscript_updates',
                'final_panel_comments',
                'final_student_replies',
                'final_panel_remarks',
                'final_panel_signatures',
                'dean_final_monitoring_signature',
            ]);
        });
    }
}
