<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->uuid("delivery_id");
            $table->foreign("delivery_id")->references("id")->on("deliveries");
            $table->dateTime("start_delivery_date")->nullable();
            $table->dateTime("complete_delivery_date")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_deliveries');
    }
}
