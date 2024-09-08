<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->bigIncrements('stock_movement_id');
            $table->string('user_id');
            $table->string('merchant_id');
            $table->string('item_id');
            $table->string('transferring_store_id');
            $table->string('receiving_store_id');
            $table->string('qty_id');
            $table->decimal('quantity',10,4);
            $table->decimal('transferring_store_old_quantity',10,4);
            $table->decimal('transferring_store_new_quantity',10,4);
            $table->decimal('receiving_store_old_quantity',10,4);
            $table->decimal('receiving_store_new_quantity',10,4);
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
        Schema::dropIfExists('stock_movements');
    }
}
