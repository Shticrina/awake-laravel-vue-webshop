<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->smallInteger('user_id')->unsigned()->nullable();
             $table->string('user_session', 200)->nullable();
            $table->double('total_price')->unsigned()->default(0);
            $table->boolean('payment_status')->default(FALSE);
            $table->boolean('is_delivered')->default(FALSE);
            $table->string('shipping_address', 191)->nullable();
            $table->string('shipping_country', 160)->nullable();
            $table->string('shipping_city', 160)->nullable();
            $table->string('shipping_postal_code', 60)->nullable();
            
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
        Schema::dropIfExists('orders');
    }
}
