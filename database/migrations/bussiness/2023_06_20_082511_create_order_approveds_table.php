<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderApprovedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_approveds', function (Blueprint $table) {
            $table->uuid('order_id')->unique();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->dateTime("sap_so_coordinate_approval_date");
            $table->dateTime("sap_so_finance_approval_date");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_approveds');
    }
}
