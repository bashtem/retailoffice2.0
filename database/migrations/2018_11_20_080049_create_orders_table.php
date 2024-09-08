<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->string('order_no', 25)->unique();
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('cus_id', 25);
            $table->string('payment_id', 25);
            $table->string('qty_id', 25);
            $table->string('user_id', 25);
            $table->enum('order_status',['SUCCESS','PENDING','CANCLED'])->default('PENDING');
            $table->text('order_note')->nullable();
            $table->decimal('order_total_amount',10,4);
            $table->decimal('cash_paid',10,4);
            $table->decimal('order_total_qty',10,4);
            $table->date('order_date');
            $table->date('cancled_date')->nullable();
            $table->time('order_time');
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
        Schema::dropIfExists('orders');
    }
}
