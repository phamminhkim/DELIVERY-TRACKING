<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoDataItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('so_data_items', function (Blueprint $table) {
            $table->id();
            $table->string('so_number', 50);
            $table->unique('so_number');
            $table->foreignId('order_process_id')->constrained('order_processes');
            $table->foreignId('so_header_id')->constrained('so_headers');
            $table->string('barcode', 130)->nullable();
            $table->string('sku_sap_code', 100)->nullable();
            $table->string('sku_sap_name', 250)->nullable();
            $table->string('sku_sap_unit', 20)->nullable();
            $table->boolean('is_promotive')->default(false);
            $table->string('note', 250)->nullable();
            $table->string('customer_sku_code', 50)->nullable();
            $table->string('customer_sku_name', 250)->nullable();
            $table->string('customer_sku_unit', 20)->nullable();
            $table->float('quantity1_po')->nullable();
            $table->float('quantity2_po')->nullable();
            $table->boolean('is_inventory')->default(false);
            $table->float('price_po')->nullable();
            $table->float('amount_po')->nullable();
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
        Schema::dropIfExists('so_data_items');
    }
}
