<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->string('similarity_manuscript')->nullable();
            $table->string('original_similarity_manuscript_filename')->nullable();
            $table->string('original_similarity_certificate_filename')->nullable();
            $table->string('similarity_certificate')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('adviser_appointments', function (Blueprint $table) {
            $table->dropColumn([
                'similarity_manuscript',
                'original_similarity_manuscript_filename',
                'original_similarity_certificate_filename',
                'similarity_certificate'
            ]);
        });
    }
    
};
