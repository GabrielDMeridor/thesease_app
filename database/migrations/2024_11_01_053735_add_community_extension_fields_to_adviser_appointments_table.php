<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommunityExtensionFieldsToAdviserAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Adding the community extension link for DrPH students
            $table->string('community_extension_link')->nullable()->after('ovpri_approval');

            // Adding the community extension response status and date
            $table->boolean('community_extension_response')->default(false)->after('community_extension_link');
            $table->timestamp('community_extension_response_date')->nullable()->after('community_extension_response');
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('community_extension_link');
            $table->dropColumn('community_extension_response');
            $table->dropColumn('community_extension_response_date');
        });
    }
}
