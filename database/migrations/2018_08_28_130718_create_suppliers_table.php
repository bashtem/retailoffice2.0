<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('sup_id');
            $table->string('merchant_id');
            $table->string('store_id');
            $table->string('sup_contact_name');
            $table->string('sup_company_name');
            $table->string('sup_mobile', 25)->unique();
            $table->string('sup_mail')->unique();
            $table->string('sup_address');
            $table->string('sup_website')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}
