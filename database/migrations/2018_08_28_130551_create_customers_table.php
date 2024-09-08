<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('cus_id');
            $table->string('registered_by_user_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('cus_name');
            $table->string('cus_mobile');
            $table->string('cus_mail')->unique()->nullable();
            $table->string('cus_address');
            $table->enum('cus_type', ['WALK-IN', 'REGULAR'])->default('REGULAR');
            $table->string('payment_id');
            $table->string('cus_created_sec');
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
        Schema::dropIfExists('customers');
    }
}
