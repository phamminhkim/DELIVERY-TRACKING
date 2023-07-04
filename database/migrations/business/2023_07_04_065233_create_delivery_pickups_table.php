<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryPickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_pickups', function (Blueprint $table) {
            $table->id();
            $table->uuid('delivery_id');
            $table->foreign('delivery_id')->references('id')->on('deliveries');
            $table->dateTime("pickup_at");
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
        Schema::dropIfExists('delivery_pickups');
    }
}
