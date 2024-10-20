<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNationalityAndImmigrationToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nationality')->nullable()->after('program');
            $table->string('immigration_or_studentvisa')->nullable()->after('nationality');
            $table->string('immigration_or_studentvisa_filename')->nullable()->after('immigration_or_studentvisa');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nationality');
            $table->dropColumn('immigration_or_studentvisa');
            $table->dropColumn('immigration_or_studentvisa_filename');
        });
    }
}
