<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProcessSaleReceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_process_sale_receives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_process_sale_id');
            $table->unsignedBigInteger('received_by');
            $table->foreign('order_process_sale_id')->references('id')->on('order_process_sales')->onDelete('cascade');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('received_at');
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
        Schema::dropIfExists('order_process_sale_receives');
    }
}
