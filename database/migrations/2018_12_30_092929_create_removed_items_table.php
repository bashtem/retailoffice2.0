<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemovedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('removed_items', function (Blueprint $table) {
            $table->increments('removal_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('qty_id');
            $table->string('item_id');
            $table->string('user_id');
            $table->text('note')->nullable();
            $table->date('removal_date');
            $table->time('removal_time');
            $table->decimal('quantity',10,4)->default('0');
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
        Schema::dropIfExists('removed_items');
    }
}
