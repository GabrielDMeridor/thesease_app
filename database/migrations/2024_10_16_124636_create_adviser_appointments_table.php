<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdviserAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('adviser_appointments', function (Blueprint $table) {
            $table->id();  
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');  
            $table->foreignId('adviser_id')->constrained('users')->onDelete('cascade');  
            $table->string('appointment_type');  
            $table->string('adviser_signature')->nullable();  
            $table->string('chair_signature')->nullable();  
            $table->string('dean_signature')->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('adviser_appointments');
    }
};
