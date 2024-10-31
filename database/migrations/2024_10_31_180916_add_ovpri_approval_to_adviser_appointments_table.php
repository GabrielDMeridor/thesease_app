<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOvpriApprovalToAdviserAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Set default to 'not yet responded'
            $table->string('ovpri_approval')->nullable()->default('not yet responded');
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Rollback by dropping the column
            $table->dropColumn('ovpri_approval');
        });
    }
}
