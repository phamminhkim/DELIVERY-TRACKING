<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawPoDataItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_po_data_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_po_header_id')->constrained('raw_po_headers');
            $table->string('customer_sku_code', 50)->nullable();
            $table->string('customer_sku_name', 250)->nullable();
            $table->string('customer_sku_unit', 20)->nullable();
            $table->float('quantity1_po')->nullable();
            $table->float('quantity2_po')->nullable();
            $table->float('price_po', 18, 2)->nullable();
            $table->float('amount_po', 18, 2)->nullable();
            $table->boolean('is_deleted')->default(false);
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
        Schema::dropIfExists('raw_po_data_items');
    }
}
