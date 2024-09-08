<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_accesses', function (Blueprint $table) {
            $table->increments('access_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('user_id');
            $table->integer('transfer_count');
            $table->string('user_id_assigned');
            $table->enum('access_status',['ACTIVE','INACTIVE'])->default('ACTIVE');
            $table->date('assigned_date');
            $table->time('assigned_time');
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
        Schema::dropIfExists('transfer_accesses');
    }
}
