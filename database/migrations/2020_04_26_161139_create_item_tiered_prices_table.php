<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemTieredPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_tiered_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->integer('qty_id');
            $table->string('item_id');
            $table->decimal('qty',10,4);
            $table->decimal('price',10,2);            
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
        Schema::dropIfExists('item_tiered_prices');
    }
}
