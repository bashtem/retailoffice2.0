<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemQtyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_qty_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('store_id');
            $table->string('qty_id');
            $table->string('item_id');
            $table->decimal('old_qty',10,4);
            $table->decimal('new_qty',10,4); 
            $table->string('trans_id'); 
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
        Schema::dropIfExists('item_qty_logs');
    }
}
