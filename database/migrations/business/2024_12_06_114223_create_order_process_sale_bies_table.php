<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProcessSaleBiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_process_sale_bies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_process_sale_id');
            $table->unsignedBigInteger('processing_by');
            $table->foreign('order_process_sale_id')->references('id')->on('order_process_sales')->onDelete('cascade');
            $table->foreign('processing_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('processing_at');
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('order_process_sale_bies');
    }
}
