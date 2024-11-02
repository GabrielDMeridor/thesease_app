<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('submission_files_link')->nullable();
            $table->string('submission_files_approval')->nullable();
            $table->boolean('submission_files_response')->default(false);
        });
    }
    
    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('submission_files_link');
            $table->dropColumn('submission_files_approval');
            $table->dropColumn('submission_files_response');
        });
    }
    

};
