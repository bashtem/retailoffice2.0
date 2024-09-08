<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemPriceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_price_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('store_id');
            $table->string('item_id');
            $table->string('qty_id');
            $table->decimal('old_price',10,2);
            $table->decimal('old_min_price',10,2);
            $table->decimal('old_max_price',10,2);
            $table->decimal('new_price',10,2);
            $table->decimal('new_min_price',10,2);
            $table->decimal('new_max_price',10,2);
            $table->decimal('old_walkin_price',10,2);
            $table->decimal('new_walkin_price',10,2);
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
        Schema::dropIfExists('item_price_logs');
    }
}
