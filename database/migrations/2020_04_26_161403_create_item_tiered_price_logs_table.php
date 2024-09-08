<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemTieredPriceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_tiered_price_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id');
            $table->string('tiered_price_id');
            $table->string('store_id');
            $table->integer('qty_id');
            $table->string('item_id');
            $table->decimal('old_qty',10,4);
            $table->decimal('new_qty',10,4);
            $table->decimal('old_price',10,2);    
            $table->decimal('new_price',10,2);    
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
        Schema::dropIfExists('item_tiered_price_logs');
    }
}
