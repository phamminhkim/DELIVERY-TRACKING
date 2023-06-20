<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderReceiversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_receivers', function (Blueprint $table) {
            $table->uuid('order_id')->unique();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string("receiver_name", 120);
            $table->string("receiver_phone", 20);
            $table->string("note", 200);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_receivers');
    }
}
