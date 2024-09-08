<?php

use Illuminate\Support\Facades\Schema;
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
            $table->increments('user_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('name');
            $table->string('email', 128)->unique();
            $table->string('phone', 25)->unique();
            $table->string('username');
            $table->string('password');
            $table->string('role');
            $table->enum('status', ['ACTIVE','INACTIVE']);
            $table->rememberToken();
            $table->string('user_agent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
