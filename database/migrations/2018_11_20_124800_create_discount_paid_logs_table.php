<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountPaidLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_paid_logs', function (Blueprint $table) {
            $table->increments('discount_paid_id');
            $table->string('cus_mobile');
            $table->string('user_id');
            $table->decimal('paid_amount',10,4);
            $table->date("date_paid");
            $table->time("time_paid");
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
        Schema::dropIfExists('discount_paid_logs');
    }
}
