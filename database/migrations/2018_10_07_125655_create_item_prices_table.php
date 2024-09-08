<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('qty_id');
            $table->string('item_id');
            $table->decimal('price', 20,2);            
            $table->decimal('min_price', 20,2);            
            $table->decimal('max_price', 20,2);          
            $table->decimal('walkin_price', 20,2);          
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
        Schema::dropIfExists('item_prices');
    }
}
