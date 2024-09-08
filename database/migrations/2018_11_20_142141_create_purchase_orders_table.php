<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('purchase_id');
            $table->string('supplier_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('user_id');
            $table->string('user_id_cancled')->nullable();
            $table->string('payment_id');
            $table->string('qty_id');
            $table->text('purchase_note');
            $table->date('purchase_date');
            $table->date('cancled_date')->nullable();
            $table->time('purchase_time');
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
        Schema::dropIfExists('purchase_orders');
    }
}
