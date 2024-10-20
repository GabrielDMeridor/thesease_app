<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileUploadsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add columns to store the generated and original filenames for file uploads
            $table->string('routing_form_one')->nullable();  // Generated filename for routing form
            $table->string('original_routing_form_one_filename')->nullable();  // Original filename for routing form

            $table->string('manuscript')->nullable();  // Generated filename for manuscript
            $table->string('original_manuscript_filename')->nullable();  // Original filename for manuscript

            $table->string('adviser_appointment_form')->nullable();  // Generated filename for adviser appointment form
            $table->string('original_adviser_appointment_form_filename')->nullable();  // Original filename for adviser appointment form
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the added columns
            $table->dropColumn('routing_form_one');
            $table->dropColumn('original_routing_form_one_filename');

            $table->dropColumn('manuscript');
            $table->dropColumn('original_manuscript_filename');

            $table->dropColumn('adviser_appointment_form');
            $table->dropColumn('original_adviser_appointment_form_filename');
        });
    }
}
