<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('community_extension_service_form_path')->nullable();
            $table->string('community_accomplishment_report_path')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn([
                'community_extension_service_form_path',
                'community_accomplishment_report_path',
            ]);
        });
    }
    
};
