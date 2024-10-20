<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisapprovalCountToAdviserAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->unsignedInteger('disapproval_count')->default(0)->after('status'); // Track disapprovals
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('disapproval_count');
        });
    }
}
