<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_items', function (Blueprint $table) {
            $table->increments('discount_item_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('cus_mobile');
            $table->string('user_id_enabled');
            $table->string('user_id_disabled')->nullable();
            $table->string('item_id');
            $table->string('qty_id');
            $table->string('item_qty');
            $table->string('discount_amount');
            $table->date('enabled_date');
            $table->time('enabled_time');
            $table->date('disabled_date')->nullable();
            $table->time('disabled_time')->nullable();
            $table->date('expiry_date')->nullable();
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
        Schema::dropIfExists('discount_items');
    }
}
