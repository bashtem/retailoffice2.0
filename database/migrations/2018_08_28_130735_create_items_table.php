<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('item_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('item_name');
            $table->string('item_desc');
            $table->string('category_id');
            $table->string('default_qty_id');
            $table->string('min_stock_level');
            $table->string('manufacturer');
            $table->string('reorder_level');
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
        Schema::dropIfExists('items');
    }
}
