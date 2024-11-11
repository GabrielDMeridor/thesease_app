<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdviserAppointmentsForMultipleCommentsAndRemarks extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Ensure panel_comments and panel_remarks are JSON fields to support arrays of objects
            $table->json('panel_comments')->nullable()->change();
            $table->json('panel_remarks')->nullable()->change();
            $table->json('student_replies')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Revert changes
            $table->json('panel_comments')->nullable()->change();
            $table->json('panel_remarks')->nullable()->change();
            $table->json('student_replies')->nullable()->change();
        });
    }
}
