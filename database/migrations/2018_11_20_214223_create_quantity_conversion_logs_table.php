<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuantityConversionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quantity_conversion_logs', function (Blueprint $table) {
            $table->increments('conversion_log_id');
            $table->string('conversion_id');
            $table->string('user_id');
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
        Schema::dropIfExists('quantity_conversion_logs');
    }
}
