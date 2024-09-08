<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseExcessOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_excess_outs', function (Blueprint $table) {
            $table->increments('excess_out_id');
            $table->string('purchase_id');
            $table->string('purchase_item_id');
            $table->decimal('qty',10,4);
            $table->enum('type',['EXCESS','OUTSTANDING']);
            $table->enum('status',['SUCCESS','PENDING','CANCLED'])->default('PENDING');
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
        Schema::dropIfExists('purchase_excess_outs');
    }
}
