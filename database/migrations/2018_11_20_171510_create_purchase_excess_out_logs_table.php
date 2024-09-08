<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseExcessOutLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_excess_out_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('excess_out_id');
            $table->string('user_id');
            $table->decimal('old_qty',10,4);
            $table->decimal('new_qty',10,4);
            $table->enum('status',['SUCCESS','PENDING','CANCLED']);
            $table->date('log_date');
            $table->time('log_time');
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
        Schema::dropIfExists('purchase_excess_out_logs');
    }
}
