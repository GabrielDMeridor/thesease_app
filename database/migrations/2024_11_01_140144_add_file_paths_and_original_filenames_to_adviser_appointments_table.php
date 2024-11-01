<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilePathsAndOriginalFilenamesToAdviserAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // File path and original filename columns for each file
            $table->string('signed_routing_form_1')->nullable()->after('community_extension_response_date');
            $table->string('original_signed_routing_form_1')->nullable()->after('signed_routing_form_1');
            $table->string('proposal_manuscript')->nullable()->after('original_signed_routing_form_1');
            $table->string('original_proposal_manuscript')->nullable()->after('proposal_manuscript');
            $table->string('proposal_video_presentation')->nullable()->after('original_proposal_manuscript');
            $table->string('original_proposal_video_presentation')->nullable()->after('proposal_video_presentation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Drop the columns if rolling back
            $table->dropColumn([
                'signed_routing_form_1',
                'original_signed_routing_form_1',
                'proposal_manuscript',
                'original_proposal_manuscript',
                'proposal_video_presentation',
                'original_proposal_video_presentation',
            ]);
        });
    }
}
