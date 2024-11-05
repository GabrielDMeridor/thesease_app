<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->date('update_date_saved')->nullable(); // Nullable to allow for existing records without this date
        });
    }
    
    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn('update_date_saved');
        });
    }
    
};
