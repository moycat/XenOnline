<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solutions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('problem_id');
            $table->integer('user_id');
            $table->integer('client_id');
            $table->integer('language');
            $table->string('code');
            $table->integer('code_length');
            $table->integer('used_time');
            $table->integer('used_memory');
            $table->string('detail');
            $table->string('detail_result');
            $table->string('detail_time');
            $table->string('detail_memory');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
