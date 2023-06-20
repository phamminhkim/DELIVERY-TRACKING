<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("company_code", 4);
            $table->foreign("company_code")->references("code")->on("companies");
            $table->unsignedBigInteger("customer_id");
            $table->foreign("customer_id")->references("id")->on("customers");
            $table->string("sap_so_number", 20);
            $table->date("sap_so_created_date");
            $table->string("sap_po_number", 20);
            $table->string("sap_do_number", 20);
            $table->unsignedBigInteger("status_id");
            $table->foreign("status_id")->references("id")->on("order_statuses");
            $table->unsignedBigInteger("warehouse_id");
            $table->foreign("warehouse_id")->references("id")->on("warehouses");
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
        Schema::dropIfExists('orders');
    }
}
