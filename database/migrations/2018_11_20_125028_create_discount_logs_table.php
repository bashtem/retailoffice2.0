<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_logs', function (Blueprint $table) {
            $table->increments('discount_log_id');
            $table->string('order_id');
            $table->decimal('total_discount',10,4);
            $table->enum('discount_status',['SUCCESS','CANCLED'])->default('SUCCESS');
            $table->date('date');
            $table->time('time');
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
        Schema::dropIfExists('discount_logs');
    }
}
