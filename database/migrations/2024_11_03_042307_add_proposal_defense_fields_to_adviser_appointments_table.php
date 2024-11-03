<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProposalDefenseFieldsToAdviserAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            // Add fields for Proposal Defense only
            $table->date('proposal_defense_date')->nullable()->after('adviser_id');
            $table->time('proposal_defense_time')->nullable()->after('proposal_defense_date');
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
            $table->dropColumn(['proposal_defense_date', 'proposal_defense_time']);
        });
    }
}

