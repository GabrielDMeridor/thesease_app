<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalStatisticianFieldsToAdviserAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('final_student_statistician_response')->nullable()->after('student_statistician_response');
            $table->string('final_statistician_approval')->nullable()->after('statistician_approval');
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('final_student_statistician_response');
            $table->dropColumn('final_statistician_approval');
        });
    }
}
