<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemQtiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_qties', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('merchant_id');
            $table->bigInteger('store_id')->index();
            $table->integer('qty_id')->index();
            $table->bigInteger('item_id')->index();
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
        Schema::dropIfExists('item_qties');
    }
}
