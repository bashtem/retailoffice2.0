<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_orders', function (Blueprint $table) {
            $table->increments('credit_order_id');
            $table->string('order_id');
            $table->string('credit_id');
            $table->string('cus_mobile');
            $table->enum('credit_order_status',['OUTSTANDING','PAID'])->default('OUTSTANDING');
            $table->date('date_paid')->nullable();
            $table->time('time_paid')->nullable();
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
        Schema::dropIfExists('credit_orders');
    }
}
