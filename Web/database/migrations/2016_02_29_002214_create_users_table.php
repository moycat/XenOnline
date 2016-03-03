<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('nickname');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('avatar');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::drop('users');
    }
}
