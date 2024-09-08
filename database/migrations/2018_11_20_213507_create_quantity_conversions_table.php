<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuantityConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quantity_conversions', function (Blueprint $table) {
            $table->increments('conversion_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('item_id');
            $table->string('initial_qty_id');
            $table->string('converted_qty_id');
            $table->string('initial_qty');
            $table->string('converted_qty');
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
        Schema::dropIfExists('quantity_conversions');
    }
}
