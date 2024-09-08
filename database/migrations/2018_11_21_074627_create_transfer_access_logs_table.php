<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferAccessLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_access_logs', function (Blueprint $table) {
            $table->increments('access_log_id');
            $table->string('access_id');
            $table->string('user_id');
            $table->integer('transfer_count');
            $table->string('user_id_assigned');
            $table->enum('access_status',['ACTIVE','INACTIVE'])->default('ACTIVE');
            $table->date('assigned_date');
            $table->time('assigned_time');
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
        Schema::dropIfExists('transfer_access_logs');
    }
}
