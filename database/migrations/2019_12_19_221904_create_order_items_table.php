<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->double('price')->unsigned();
            $table->string('size', 10)->nullable();
            $table->string('color', 255)->nullable();
            $table->smallInteger('quantity')->unsigned()->default(1);
            $table->string('image', 255)->nullable();

            /*$table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onUpdate('cascade');
            
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onUpdate('cascade');*/

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
        Schema::dropIfExists('order_items');
    }
}
