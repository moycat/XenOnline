<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('content');
            $table->string('tag');
            $table->string('hash');
            $table->integer('ver');
            $table->integer('test_turn');
            $table->timestamps();
            $table->integer('time_limit');
            $table->integer('memory_limit');
            $table->integer('try');
            $table->integer('solve');
            $table->integer('submit');
            $table->integer('ac');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('problems');
    }
}
