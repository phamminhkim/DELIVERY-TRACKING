<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfirmDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirm_deliveries', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->dateTime('confirm_date');
            $table->string('confirm_img');
            $table->timestamps();            
            $table->string('drive_phone',50);
            $table->string('drive_name', 50);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('confirm_deliveries');
    }
}
