<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->integer('restaurant_id');
            $table->integer('slug');
            $table->integer('person');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->integer('restaurant_id')->after('id');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('restaurant_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
}
