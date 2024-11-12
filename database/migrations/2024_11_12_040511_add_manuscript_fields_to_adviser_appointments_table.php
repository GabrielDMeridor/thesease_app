<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManuscriptFieldsToAdviserAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('revised_manuscript_path')->nullable(); // Path for the revised manuscript
            $table->string('revised_manuscript_original_name')->nullable(); // Original file name
            $table->timestamp('uploaded_at')->nullable(); // Upload timestamp
        });
    }

    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn(['revised_manuscript_path', 'revised_manuscript_original_name', 'uploaded_at']);
        });
    }
}
