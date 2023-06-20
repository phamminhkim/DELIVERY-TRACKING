<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDriverConfirmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_driver_confirms', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->dateTime("confirm_date")->nullable();
            $table->enum("confirm_status", ["partly", "fully"]);
            $table->string("driver_phone", 20);
            $table->string("driver_name", 50);
            $table->string("driver_note", 120);
            $table->string("driver_plate_number", 20);
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
        Schema::dropIfExists('order_driver_confirms');
    }
}
