<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmDeliveryDateToOrderDeliveries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_deliveries', function (Blueprint $table) {
            $table->dateTime('confirm_delivery_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_deliveries', function (Blueprint $table) {
            $table->dropColumn('confirm_delivery_date');
        });
    }
}
