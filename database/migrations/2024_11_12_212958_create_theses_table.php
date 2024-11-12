<?php

// database/migrations/xxxx_xx_xx_create_theses_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThesesTable extends Migration
{
    public function up()
    {
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path');          // Path to the uploaded file
            $table->year('year_published');       // Year of publication
            $table->string('program');            // Program name, e.g., MAEd, PhD-CI-ELT
            $table->string('degree_type');        // Degree type, e.g., 'Masteral' or 'Doctorate'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('theses');
    }
}
