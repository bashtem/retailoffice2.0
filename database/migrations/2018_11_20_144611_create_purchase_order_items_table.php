<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->increments('purchase_item_id');
            $table->string('purchase_id');
            $table->string('item_id');
            $table->decimal('purchase_qty',10,4);
            $table->decimal('purchase_price',10,4);
            $table->string('confirm_user_id')->nullable();
            $table->string('user_id_cancled')->nullable();
            $table->enum('purchase_status',['SUCCESS','PENDING','CANCLED'])->default('PENDING');
            $table->date('confirm_date')->nullable();
            $table->date('cancled_date')->nullable();
            $table->time('confirm_time')->nullable();
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
        Schema::dropIfExists('purchase_order_items');
    }
}
