<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_sales', function (Blueprint $table) {
            $table->uuid('order_id')->unique();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedBigInteger("distribution_channel_id");
            $table->foreign("distribution_channel_id")->references("id")->on("distribution_channels");
            $table->unsignedBigInteger("sale_district_id");
            $table->foreign("sale_district_id")->references("id")->on("sale_districts");
            $table->unsignedBigInteger("sale_group_id");
            $table->foreign("sale_group_id")->references("id")->on("sale_groups");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_sales');
    }
}
