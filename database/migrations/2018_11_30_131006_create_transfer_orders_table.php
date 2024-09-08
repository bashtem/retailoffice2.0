<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_orders', function (Blueprint $table) {
            $table->increments('transfer_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('user_id_transfer');
            $table->string('user_id_cancled')->nullable();
            $table->string('user_id_confirmed')->nullable();
            $table->date('transfer_date');
            $table->time('transfer_time');
            $table->enum('transfer_status',['SUCCESS','CANCLED','PENDING'])->default('PENDING');
            $table->date('confirmed_date')->nullable();
            $table->time('confirmed_time')->nullable();
            $table->date('cancled_date')->nullable();
            $table->time('cancled_time')->nullable();
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
        Schema::dropIfExists('transfer_orders');
    }
}
