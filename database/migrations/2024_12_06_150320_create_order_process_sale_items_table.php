<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProcessSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_process_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_process_sale_id');
            // add khóa ngoại
            $table->foreign('order_process_sale_id')->references('id')->on('order_process_sales')->onDelete('cascade');
            $table->string('customer_key');
            $table->string('type')->nullable();
            $table->string('customer_name');
            $table->string('barcode');
            $table->string('product_name');
            $table->float('price', 18, 2);
            $table->float('quantity', 18, 2);
            $table->integer('specifications')->nullable();
            $table->string('description')->nullable();
            $table->string('count_order')->nullable();
            $table->string('barcode_cty');
            $table->string('sap_code');
            $table->string('sap_name');
            $table->string('unit');
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
        Schema::dropIfExists('order_process_sale_items');
    }
}
