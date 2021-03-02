<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('type')->unsigned();
            $table->string('type_name', 60);
            $table->integer('order_id')->unsigned()->unique();
            $table->integer('user_id')->unsigned();
            $table->smallInteger('amount')->unsigned();
            $table->string('stripe_customer', 100)->nullable();
            $table->string('stripe_source', 100)->nullable();
            $table->string('stripe_charge', 100)->nullable();
            $table->string('transfer_comm', 100)->nullable(); 
            $table->tinyInteger('status')->unsigned()->default(0);
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
        Schema::dropIfExists('payments');
    }
}
