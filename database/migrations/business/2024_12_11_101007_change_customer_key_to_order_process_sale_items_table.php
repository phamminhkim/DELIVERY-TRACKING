<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCustomerKeyToOrderProcessSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_process_sale_items', function (Blueprint $table) {
            // tôi muốn chuyển cột customer_key thành nullable
            $table->string('customer_key')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_process_sale_items', function (Blueprint $table) {
            $table->string('customer_key')->change();
        });
    }
}
