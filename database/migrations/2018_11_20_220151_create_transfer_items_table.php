<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_items', function (Blueprint $table) {
            $table->increments('transfer_item_id');
            $table->string('transfer_id');
            $table->string('user_id_cancled')->nullable();
            $table->string('user_id_confirmed')->nullable();
            $table->string('conversion_id');
            $table->string('item_id');
            $table->decimal('transfer_qty',10,4);
            $table->decimal('transferred_qty',10,4);
            $table->enum('transfer_status',['SUCCESS','CANCLED','PENDING'])->default('PENDING');
            $table->date('cancled_date')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->time('confirmed_time')->nullable();
            $table->time('cancled_time')->nullable();
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
        Schema::dropIfExists('transfer_items');
    }
}