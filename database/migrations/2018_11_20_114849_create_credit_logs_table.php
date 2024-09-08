<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_logs', function (Blueprint $table) {
            $table->increments('credit_log_id');
            $table->string('credit_order_id')->nullable();
            $table->string('user_id');
            $table->string('credit_id');
            $table->enum('credit_log_status',['CREDIT','ORDER','PAID','DEBIT']);
            $table->decimal('old_credit',10,4);
            $table->decimal('new_credit',10,4);
            $table->date('credit_date');
            $table->time('credit_time');
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
        Schema::dropIfExists('credit_logs');
    }
}
