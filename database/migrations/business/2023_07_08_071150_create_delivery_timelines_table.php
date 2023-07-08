<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_timelines', function (Blueprint $table) {
            $table->id();
            $table->uuid('delivery_id');
            $table->foreign('delivery_id')->references('id')->on('deliveries');
            $table->timestamp('timestamp');
            $table->string('event');
            $table->string('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_timelines');
    }
}
